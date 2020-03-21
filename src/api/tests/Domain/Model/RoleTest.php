<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\Right;
use App\Domain\Model\Role;
use App\Tests\Domain\DomainTestCase;
use function count;

final class RoleTest extends DomainTestCase
{
    public function testSetRoleName(): void
    {
        $role = new Role($this->faker->name);

        $name = $this->faker->name;
        $role->setName($name);
        $this->assertEquals($name, $role->getName());

        $this->expectException(InvalidStringValue::class);
        $role->setName('');

        $this->expectException(InvalidStringValue::class);
        $role->setName($this->faker->text(256));
    }

    public function testSetRoleDescription(): void
    {
        $role = new Role($this->faker->name);

        $description = $this->faker->text(1200);
        $role->setDescription($description);
        $this->assertEquals($description, $role->getDescription());

        $this->expectException(InvalidStringValue::class);
        $role->setDescription('');
    }

    public function testSetRoleRights(): void
    {
        $role = new Role($this->faker->name);
        $rights = [new Right('Test Right', 'TEST_RIGHT')];

        $role->setRights($rights);
        $this->assertEquals(count($role->getRights()), count($rights));

        $this->expectException(InvalidArrayValue::class);
        $role->setRights([new Role($this->faker->name)]);
    }
}
