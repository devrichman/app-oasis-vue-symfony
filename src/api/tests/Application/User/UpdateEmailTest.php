<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\UpdateEmail;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\Exist;
use App\Domain\Model\User;
use App\Domain\Repository\EmailTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use function preg_quote;

final class UpdateEmailTest extends ApplicationTestCase
{
    private UpdateEmail $updateEmail;
    private EmailTokenRepository $emailTokenRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->updateEmail = self::$container->get(UpdateEmail::class);
        $this->emailTokenRepository = self::$container->get(EmailTokenRepository::class);
    }

    /**
     * @param mixed[] $updateData
     *
     * @dataProvider updateEmailAddressDataProvider
     */
    public function testUpdateEmail(array $updateData, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $previousEmail = $this->loggedUser->getEmail();

        $this->updateEmail->updateEmail(
            $updateData['id'] ?? $this->loggedUser->getId(),
            $updateData['email'],
        );

        $emailToken = $this->emailTokenRepository->findOneByUserId($this->loggedUser->getId());

        $this->assertEquals($this->loggedUser->getEmail(), $previousEmail);
        $this->assertEquals($emailToken->getUser()->getId(), $this->loggedUser->getId());
    }

    public function testDuplicateEmail(): void
    {
        $email = $this->faker->email;

        $user = new User(
            self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR),
            $this->faker->firstName,
            $this->faker->lastName,
            $email,
            $this->faker->phoneNumber,
        );
        self::$container->get(UserRepository::class)->save($user);

        $this->expectException(Exist::class);
        $this->updateEmail->updateEmail($user->getId(), $email);
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
