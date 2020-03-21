<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\AddRoleToUser;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function count;

final class AddRoleToUserTest extends ApplicationTestCase
{
    private AddRoleToUser $addRoleToUser;
    private UserRepository $userRepository;
    private User $user;
    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addRoleToUser = self::$container->get(AddRoleToUser::class);
        $this->userRepository = self::$container->get(UserRepository::class);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::CANDIDATE);
        $this->user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->role = new Role($this->faker->name);

        $this->userRepository->save($this->user);
        self::$container->get(RoleRepository::class)->save($this->role);
    }

    public function testAddRole(): void
    {
        $this->addRoleToUser->add($this->user->getId(), $this->role->getId());

        $user = $this->userRepository->mustFindOneById($this->user->getId());
        $userRoles = $user->getRoles();
        $this->assertEquals(count($userRoles), 1);
        $this->assertEquals($userRoles[0]->getId(), $this->role->getId());

        $this->expectException(NotFound::class);
        $this->addRoleToUser->add(Uuid::uuid1()->toString(), $this->role->getId());

        $this->expectException(NotFound::class);
        $this->addRoleToUser->add($this->user->getId(), Uuid::uuid1()->toString());
    }
}
