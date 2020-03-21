<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Enum\RoleEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use Symfony\Component\Console\Output\OutputInterface;

trait CreateUser
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserTypeRepository $userTypeRepository;

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     */
    private function create(OutputInterface $output, string $firstName, string $lastName, string $phone, string $email, string $password): void
    {
        $output->writeln('creating test user...');

        $existingUser = $this->userRepository->findOneByEmail($email);
        if ($existingUser !== null) {
            $output->writeln('it seems the user already exists');

            return;
        }

        $type = $this->userTypeRepository->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $user = new User($type, $firstName, $lastName, $email, $phone);
        $user->setPassword($password);

        $role = $this->roleRepository->mustFindOneById(RoleEnum::ADMINISTRATEUR_ID);
        $user->addRoleByUsersRoles($role);

        $this->userRepository->saveNoLog($user);

        $output->writeln('test user created!');
    }
}
