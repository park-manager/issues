ifndef BUILD_ENV
BUILD_ENV=php7.3
endif

QA_DOCKER_IMAGE=parkmanager/phpqa:latest
QA_DOCKER_COMMAND=docker run --init --interactive --tty --rm --env "COMPOSER_HOME=/composer" --user "$(shell id -u):$(shell id -g)" --volume /tmp/tmp-phpqa-$(shell id -u):/tmp:delegated --volume "$(shell pwd):/project:delegated" --volume "${HOME}/.composer:/composer:delegated" --workdir /project ${QA_DOCKER_IMAGE}

install: composer-install
dist: composer-validate cs phpstan psalm test
ci: check test
check: composer-validate lint-xml lint-yaml lint-twig cs-check phpstan psalm
test: phpunit-coverage # infection

clean:
	rm -rf var/

composer-validate: ensure
	@echo "Validating local composer files"
	@sh -c "${QA_DOCKER_COMMAND} composer validate"
#	@sh -c "${QA_DOCKER_COMMAND} composer normalize"

	@for direc in $$(gfind src -mindepth 2 -type f -name composer.json -printf '%h\n'); \
	do \
		sh -c "${QA_DOCKER_COMMAND} composer validate --working-dir=$${direc}"; \
		sh -c "${QA_DOCKER_COMMAND} composer normalize --working-dir=$${direc}"; \
	done;

#	@for direc in $$(gfind src -mindepth 2 -type f -name composer.json -printf '%h\n'); \
#	do \
#		sh -c "${QA_DOCKER_COMMAND} composer validate --working-dir=$${direc}"; \
#		sh -c "${QA_DOCKER_COMMAND} composer normalize --working-dir=$${direc}"; \
#	done;

lint-xml:
	@echo "Validating XML files"

ifeq (, $(shell which xmllint))
	@echo "[SKIPPED] No xmllint in $(PATH), consider installing it"
else
	@find . \( -name '*.xml' -or -name '*.xliff' -or -name '*.xlf' \) \
			-not -path './vendor/*' \
			-not -path './vendor-bin/*' \
			-not -path './.*' \
			-not -path './var/*' \
			-type f \
			-exec xmllint --format --encode UTF-8 --noout '{}' \;
endif

lint-yaml:
	@echo "Validating YAML files"
	@sh -c "${QA_DOCKER_COMMAND} php bin/console lint:yaml -vv src/"
	@sh -c "${QA_DOCKER_COMMAND} php bin/console lint:yaml -vv config/"

lint-twig:
	@echo "Validating Twig files"
	@sh -c "${QA_DOCKER_COMMAND} php bin/console lint:twig -vv src/"
	@sh -c "${QA_DOCKER_COMMAND} php bin/console lint:twig -vv templates/"

composer-install: fetch ensure clean
	sh -c "${QA_DOCKER_COMMAND} composer upgrade"

composer-install-lowest: fetch ensure clean
	sh -c "${QA_DOCKER_COMMAND} composer upgrade --prefer-lowest"

composer-install-dev: fetch ensure clean
	rm -f composer.lock
	cp composer.json _composer.json
	sh -c "${QA_DOCKER_COMMAND} composer config minimum-stability dev"
	sh -c "${QA_DOCKER_COMMAND} composer upgrade --no-progress --no-interaction --no-suggest --optimize-autoloader --ansi"
	mv _composer.json composer.json

cs:
	sh -c "docker-compose run --rm php vendor/bin/phpcbf"

cs-check:
	sh -c "docker-compose run --rm php vendor/bin/phpcs"

phpstan: ensure
	sh -c "${QA_DOCKER_COMMAND} phpstan analyse"

psalm: ensure
	sh -c "${QA_DOCKER_COMMAND} psalm --show-info=false"

infection: ensure
	sh -c "${QA_DOCKER_COMMAND} phpdbg -qrr vendor/bin/phpunit --verbose --log-junit=var/phpunit.junit.xml --coverage-xml var/coverage-xml/"
	sh -c "${QA_DOCKER_COMMAND} phpdbg -qrr /tools/infection run --verbose --show-mutations --no-interaction --only-covered --coverage var/ --min-msi=84 --min-covered-msi=84"

phpunit-coverage: ensure
	docker-compose run --rm php make db-fixtures
	docker-compose run --rm php phpdbg -qrr vendor/bin/phpunit --verbose --exclude-group "" --coverage-text --log-junit=var/phpunit.junit.xml --coverage-xml var/coverage-xml/

db-fixtures:
	bin/console doctrine:database:drop --force || true
	bin/console doctrine:database:create
	bin/console doctrine:schema:validate || true
	bin/console doctrine:schema:update --force

phpunit:
	docker-compose run --rm php make db-fixtures
	docker-compose run --rm php phpunit --verbose --exclude-group ""

ensure:
	mkdir -p ${HOME}/.composer /tmp/tmp-phpqa-$(shell id -u)

fetch:
	docker pull "${QA_DOCKER_IMAGE}"
