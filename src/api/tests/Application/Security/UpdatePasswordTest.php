<?php

declare(strict_types=1);

namespace App\Tests\Application\Role;

use App\Application\Security\UpdatePassword;
use App\Domain\Enum\ResetPasswordTokenEnum;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Tests\Application\ApplicationTestCase;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use function password_verify;
use function preg_quote;
use function time;

class UpdatePasswordTest extends ApplicationTestCase
{
    protected UpdatePassword $updatePassword;
    protected ResetPasswordTokenRepository $resetPasswordTokenRepository;
    protected UserRepository $userRepository;
    protected EnvVarHelper $envVarHelper;

    protected ResetPasswordToken $testToken;
    protected string $accessToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updatePassword = self::$container->get(UpdatePassword::class);
        $this->resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
        $this->userRepository = self::$container->get(UserRepository::class);
        $this->envVarHelper = self::$container->get(EnvVarHelper::class);

        // Create a test token to verify later
        $this->accessToken = Uuid::uuid1()->toString();
        $this->testToken = new ResetPasswordToken($this->loggedUser, $this->accessToken);
        $this->resetPasswordTokenRepository->save($this->testToken);
    }

    public function testUpdatePassword(): void
    {
        $tokenPassword = $this->encodeJwt($this->accessToken, $this->loggedUser->getId());

        $user = $this->updatePassword->update($tokenPassword, 'Password1');
        $this->assertTrue(password_verify('Password1', $user->getPassword()));
        $this->assertNull($this->resetPasswordTokenRepository->findOneByUser($this->loggedUser));

        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(ResetPasswordToken::class) . '/');
        $this->updatePassword->update('invalid password token', 'Password1');
    }

    public function testInvalidToken(): void
    {
        // User tries to send a valid JWT but with a invalid access token
        $this->expectException(InvalidValue::class);
        $this->updatePassword->update($this->encodeJwt('invalid access token', $this->loggedUser->getId()), 'Password1');
    }

    public function testInvalidTokenEmail(): void
    {
        // User tries to send a valid JWT with an invalid user
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(ResetPasswordToken::class) . '/');
        $this->updatePassword->update($this->encodeJwt($this->accessToken, 'someone@example.com'), 'Password1');
    }

    protected function encodeJwt(string $accessToken, string $aud): string
    {
        return JWT::encode([
            'sub' => 'reset',
            'exp' => time() + (3600 * 24),
            'aud' => $aud,
            'accessToken' => $accessToken,
        ], $this->envVarHelper->fetch(ResetPasswordTokenEnum::SECRET_ENV), ResetPasswordTokenEnum::ALGO);
    }
}
