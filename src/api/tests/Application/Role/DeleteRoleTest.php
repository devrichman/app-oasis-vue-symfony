<?php

declare(strict_types=1);

namespace App\Tests\Application\Role;

use App\Application\Role\DeleteRole;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;

class DeleteRoleTest extends ApplicationTestCase
{
    protected DeleteRole $deleteRole;
    protected RoleRepository $roleRepository;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteRole = self::$container->get(DeleteRole::class);
        $this->roleRepository = self::$container->get(RoleRepository::class);
        $this->userRepository = self::$container->get(UserRepository::class);
    }

    public function testDeleteRole(): void
    {
        $role = new Role($this->faker->name);
        $this->roleRepository->save($role);
        $roleId = $role->getId();
        $this->deleteRole->delete($roleId);

        $this->expectException(NotFound::class);
        $this->roleRepository->mustFindOneById($roleId);

        $role = new Role($this->faker->name);
        $this->roleRepository->save($role);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $user->addRoleByUsersRoles($role);
        $this->userRepository->save($user);

        $this->expectException(InvalidValue::class);
        $this->deleteRole->delete($roleId);
    }
}
