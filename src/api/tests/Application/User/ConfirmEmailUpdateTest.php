<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\ConfirmEmailUpdate;
use App\Domain\Model\UpdateEmailToken;
use App\Domain\Repository\EmailTokenRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function preg_quote;

final class ConfirmEmailUpdateTest extends ApplicationTestCase
{
    private ConfirmEmailUpdate $confirmEmailUpdate;
    private EmailTokenRepository $emailTokenRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->confirmEmailUpdate = self::$container->get(ConfirmEmailUpdate::class);
        $this->emailTokenRepository = self::$container->get(EmailTokenRepository::class);
    }

    /**
     * @param mixed[] $confirmData
     *
     * @dataProvider updateEmailAddressDataProvider
     */
    public function testConfirmEmailUpdate(array $confirmData, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $token = Uuid::uuid1()->toString();

        $jwt = $this->emailTokenRepository->encodeToken($this->loggedUser, $token);

        $emailToken = new UpdateEmailToken($this->loggedUser, $token, $confirmData['email']);
        $this->emailTokenRepository->save($emailToken);

        $this->confirmEmailUpdate->confirmEmail($jwt);

        $emailToken = $this->emailTokenRepository->findOneByUserId($this->loggedUser->getId());

        $this->assertNull($emailToken);
        $this->assertEquals($confirmData['email'], $this->loggedUser->getEmail());
    }

    /**
     * @return mixed[]
     */
    public function updateEmailAddressDataProvider(): array
    {
        return [
            'Update email' => [
                ['email' => 'test@gmail.com'],
            ],
        ];
    }
}
