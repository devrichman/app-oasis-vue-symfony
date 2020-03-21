<?php

declare(strict_types=1);

namespace App\Tests\Application\Role;

use App\Application\Role\UpdateRole;
use App\Domain\Enum\RightEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Right;
use App\Domain\Model\Role;
use App\Domain\Repository\RoleRepository;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use function array_map;
use function count;
use function preg_quote;

class UpdateRoleTest extends ApplicationTestCase
{
    protected UpdateRole $updateRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateRole = self::$container->get(UpdateRole::class);
    }

    /**
     * @param string[] $rights
     *
     * @dataProvider updateRoleDataProvider
     */
    public function testUpdateRole(?string $id, string $name, string $description, array $rights, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $role = $this->updateRole->update(
            $id ?? RoleEnum::ADMINISTRATEUR_ID,
            $name,
            $description,
            $rights
        );

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($role->getName(), $name);
        $this->assertEquals($role->getDescription(), $description);

        $this->assertEquals(count($role->getRights()), count($rights));
        foreach ($rights as $right) {
            $this->assertContains($right, array_map(static fn(Right $right) => $right->getCode(), $role->getRights()));
        }
    }

    public function testUpdateRoleDuplicateName(): void
    {
        $role = new Role($this->faker->name);
        self::$container->get(RoleRepository::class)->save($role);

        $this->expectException(Exist::class);
        $this->updateRole->update($role->getId(), RoleEnum::ADMINISTRATEUR_NAME, $this->faker->text(255), [RightEnum::CREATE_USER_CODE]);
    }

    /**
     * @return mixed[]
     */
    public function updateRoleDataProvider(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            [null, $faker->name, $faker->text(255), [RightEnum::CREATE_USER_CODE]],
            [Uuid::uuid1()->toString(), $faker->name, $faker->text(255), [RightEnum::CREATE_USER_CODE], NotFound::class, Role::class],
            [null, $faker->name, $faker->text(255), ['INVALID_RIGHT', RightEnum::CREATE_USER_CODE], NotFound::class, Right::class],
            [null, RoleEnum::ADMINISTRATEUR_NAME, $faker->text(255), [RightEnum::CREATE_USER_CODE]],
        ];
    }
}
