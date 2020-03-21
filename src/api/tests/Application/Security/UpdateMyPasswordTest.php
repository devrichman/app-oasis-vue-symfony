<?php

declare(strict_types=1);

namespace App\Tests\Application\Role;

use App\Application\Security\UpdateMyPassword;
use App\Domain\Repository\UserRepository;
use App\Tests\Application\ApplicationTestCase;
use function password_verify;

class UpdateMyPasswordTest extends ApplicationTestCase
{
    protected UpdateMyPassword $updateMyPassword;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateMyPassword = self::$container->get(UpdateMyPassword::class);
        $this->userRepository = self::$container->get(UserRepository::class);
    }

    public function testUpdatePassword(): void
    {
        $user = $this->userRepository->getLoggedUser();

        $this->updateMyPassword->updateMyPassword('Password1');

        $this->assertTrue(password_verify('Password1', $user->getPassword()));
    }
}
