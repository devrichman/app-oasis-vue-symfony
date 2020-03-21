<?php

declare(strict_types=1);

namespace App\Tests\Application\Role;

use App\Application\Security\ResetPassword;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Tests\Application\ApplicationTestCase;

class ResetPasswordTest extends ApplicationTestCase
{
    protected ResetPassword $resetPassword;
    protected ResetPasswordTokenRepository $resetPasswordTokenRepository;
    protected EnvVarHelper $envVarHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetPassword = self::$container->get(ResetPassword::class);
        $this->resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
        $this->envVarHelper = self::$container->get(EnvVarHelper::class);
    }

    public function testResetPasswordToken(): void
    {
        $email = $this->resetPassword->reset($this->loggedUser->getEmail());

        $token = $this->resetPasswordTokenRepository->findOneByUser($this->loggedUser);

        $this->assertNotNull($email);
        $this->assertNotNull($token);
    }
}
