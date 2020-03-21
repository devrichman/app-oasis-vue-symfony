<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Command\CreateTestUserCommand;
use App\Infrastructure\Security\UserProvider;
use Doctrine\DBAL\Connection;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use function serialize;

abstract class ApplicationTestCase extends WebTestCase
{
    protected Generator $faker;
    protected Connection $dbal;
    protected User $loggedUser;
    private UserProvider $userProvider;
    private TokenStorageInterface $tokenStorage;
    private SessionInterface $session;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->faker = Factory::create('fr_FR');
        $this->dbal = self::$container->get(Connection::class);
        $this->userProvider = self::$container->get(UserProvider::class);
        $this->tokenStorage = self::$container->get(TokenStorageInterface::class);
        $this->session = self::$container->get(SessionInterface::class);
        $this->dbal->beginTransaction();
        $this->logIn();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->dbal->rollBack();
        parent::tearDown();
    }

    private function logIn(): void
    {
        $user = $this->userProvider->loadUserByUsername(CreateTestUserCommand::EMAIL);
        $this->loggedUser = self::$container->get(UserRepository::class)->mustFindOneById($user->getId());

        // Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->session->set('_security_main', serialize($token));
    }
}
