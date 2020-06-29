<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Tests\Infrastructure\Doctrine\Repository;

use ParkManager\Domain\Webhosting\Constraint\Constraints;
use ParkManager\Domain\Webhosting\Constraint\ConstraintSetId;
use ParkManager\Domain\Webhosting\Constraint\Exception\ConstraintSetNotFound;
use ParkManager\Domain\Webhosting\Constraint\SharedConstraintSet;
use ParkManager\Infrastructure\Doctrine\Repository\SharedConstraintSetOrmRepository;
use ParkManager\Tests\Infrastructure\Doctrine\EntityRepositoryTestCase;

/**
 * @internal
 *
 * @group functional
 */
final class SharedConstraintSetOrmRepositoryTest extends EntityRepositoryTestCase
{
    private const SET_ID1 = '2570c850-a5e0-11e7-868d-acbc32b58315';
    private const SET_ID2 = '3bd0fa08-a756-11e7-bdf0-acbc32b58315';

    /** @test */
    public function it_gets_existing_constraint_sets(): void
    {
        $repository = $this->createRepository();
        $this->setUpConstraintSet1($repository);
        $this->setUpConstraintSet2($repository);

        $id = ConstraintSetId::fromString(self::SET_ID1);
        $id2 = ConstraintSetId::fromString(self::SET_ID2);

        $constraintSet = $repository->get($id);
        $constraintSet2 = $repository->get($id2);

        self::assertEquals($id, $constraintSet->getId());
        self::assertEquals(['title' => 'Supper Gold XL'], $constraintSet->getMetadata());
        self::assertTrue($constraintSet->getConstraints()->equals((new Constraints())->setMonthlyTraffic(5)));

        self::assertEquals($id2, $constraintSet2->getId());
        self::assertEquals([], $constraintSet2->getMetadata());
        self::assertTrue($constraintSet2->getConstraints()->equals((new Constraints())->setMonthlyTraffic(5)));
    }

    /** @test */
    public function it_removes_an_existing_constraint_set(): void
    {
        $repository = $this->createRepository();
        $this->setUpConstraintSet1($repository);
        $this->setUpConstraintSet2($repository);

        $id = ConstraintSetId::fromString(self::SET_ID1);
        $id2 = ConstraintSetId::fromString(self::SET_ID2);
        $constraintSet = $repository->get($id);

        $repository->remove($constraintSet);

        $repository->get($id2);

        // Assert actually removed
        $this->expectException(ConstraintSetNotFound::class);
        $this->expectExceptionMessage(ConstraintSetNotFound::withId($id)->getMessage());
        $repository->get($id);
    }

    private function createRepository(): SharedConstraintSetOrmRepository
    {
        return new SharedConstraintSetOrmRepository($this->getEntityManager());
    }

    private function setUpConstraintSet1(SharedConstraintSetOrmRepository $repository): void
    {
        $constraintSet = new SharedConstraintSet(
            ConstraintSetId::fromString(self::SET_ID1),
            (new Constraints())->setMonthlyTraffic(5)
        );
        $constraintSet->withMetadata(['title' => 'Supper Gold XL']);

        $repository->save($constraintSet);
    }

    private function setUpConstraintSet2(SharedConstraintSetOrmRepository $repository): void
    {
        $repository->save(
            new SharedConstraintSet(
                ConstraintSetId::fromString(self::SET_ID2),
                (new Constraints())->setMonthlyTraffic(5)
            )
        );
    }
}