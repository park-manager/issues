<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Bundle\WebhostingBundle\Tests\Model\Plan;

use ParkManager\Bundle\WebhostingBundle\Model\Plan\Constraints;
use ParkManager\Bundle\WebhostingBundle\Model\Plan\Exception\ConstraintNotInSet;
use ParkManager\Bundle\WebhostingBundle\Tests\Fixtures\PlanConstraint\MonthlyTrafficQuota;
use ParkManager\Bundle\WebhostingBundle\Tests\Fixtures\PlanConstraint\StorageSpaceQuota;
use PHPUnit\Framework\TestCase;
use function get_class;
use function iterator_to_array;

/**
 * @internal
 */
final class ConstraintsTest extends TestCase
{
    /** @test */
    public function its_constructable(): void
    {
        $constraint   = new StorageSpaceQuota('9B');
        $constraint2  = new MonthlyTrafficQuota(50);
        $constraints = new Constraints($constraint, $constraint);

        self::assertConstraintsEquals([$constraint], $constraints);
        self::assertTrue($constraints->has(get_class($constraint)));
        self::assertFalse($constraints->has(get_class($constraint2)));
        self::assertEquals($constraint, $constraints->get(StorageSpaceQuota::class));
    }

    /** @test */
    public function it_throws_when_getting_unset_constraint(): void
    {
        $constraint   = new StorageSpaceQuota('9B');
        $constraints = new Constraints($constraint);

        $this->expectException(ConstraintNotInSet::class);
        $this->expectExceptionMessage(ConstraintNotInSet::withName(MonthlyTrafficQuota::class)->getMessage());

        $constraints->get(MonthlyTrafficQuota::class);
    }

    /** @test */
    public function it_allows_adding_and_returns_new_set(): void
    {
        $constraint  = new StorageSpaceQuota('9B');
        $constraint2 = new MonthlyTrafficQuota(50);

        $constraints    = new Constraints($constraint);
        $constraintsNew = $constraints->add($constraint2);

        self::assertNotSame($constraints, $constraintsNew);
        self::assertConstraintsEquals([$constraint], $constraints);
        self::assertConstraintsEquals([$constraint, $constraint2], $constraintsNew);
    }

    /** @test */
    public function it_allows_removing_and_returns_new_set(): void
    {
        $constraint  = new StorageSpaceQuota('9B');
        $constraint2 = new MonthlyTrafficQuota(50);

        $constraints    = new Constraints($constraint, $constraint2);
        $constraintsNew = $constraints->remove($constraint);

        self::assertNotSame($constraints, $constraintsNew);
        self::assertConstraintsEquals([$constraint, $constraint2], $constraints);
        self::assertConstraintsEquals([$constraint2], $constraintsNew);
    }

    private static function assertConstraintsEquals(array $constraints, Constraints $constraintsSet): void
    {
        $processedConstraints = [];
        foreach ($constraints as $constraint) {
            $processedConstraints[get_class($constraint)] = $constraint;
        }

        self::assertEquals($processedConstraints, iterator_to_array($constraintsSet));
    }
}
