<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\EmailUnique;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\Exist;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;

final class EmailUniqueTest extends ApplicationTestCase
{
    private EmailUnique $emailUnique;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailUnique = self::$container->get(EmailUnique::class);
    }

    public function testEmailNotUnique(): void
    {
        $this->expectException(Exist::class);

        $email = $this->faker->email;

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->user = new User($userType, $this->faker->firstName, $this->faker->lastName, $email, $this->faker->phoneNumber);
        $this->user->setStatus(true);

        self::$container->get(UserRepository::class)->save($this->user);

        $this->emailUnique->emailUnique($email, $this->loggedUser->getId());
    }

    public function testEmailNotUniqueSameAsUser(): void
    {
        $result = $this->emailUnique->emailUnique($this->loggedUser->getEmail(), $this->loggedUser->getId());

        $this->assertTrue($result);
    }

    public function testEmailUnique(): void
    {
        $email = $this->faker->email;

        $result = $this->emailUnique->emailUnique($email, $this->loggedUser->getId());

        $this->assertTrue($result);
    }
}
