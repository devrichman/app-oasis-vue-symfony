<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Infrastructure\Config\EnvVarHelper;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateTestUserCommand extends Command
{
    use CreateUser;

    private const FIRST_NAME = 'Datum';
    private const LAST_NAME = 'Data';
    public const EMAIL = 'test@oasys.localhost';
    public const PHONE = '+33123456789';
    private const PASSWORD = 'Secret93';

    private Connection $dbal;
    private EnvVarHelper $envVarHelper;
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserTypeRepository $userTypeRepository;

    public function __construct(Connection $dbal, EnvVarHelper $envVarHelper, UserRepository $userRepository, RoleRepository $roleRepository, UserTypeRepository $userTypeRepository)
    {
        $this->dbal = $dbal;
        $this->envVarHelper = $envVarHelper;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userTypeRepository = $userTypeRepository;
        parent::__construct('users:create-test-user');
    }

    public function configure(): void
    {
        $this->setDescription('Creates the test user.')
            ->setHelp('This command allows you to create the test admin user for the tests.');
    }

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->create(
            $output,
            self::FIRST_NAME,
            self::LAST_NAME,
            self::PHONE,
            self::EMAIL,
            self::PASSWORD,
        );

        return 0;
    }
}
