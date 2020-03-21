<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\AcceptCgu;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use function preg_quote;

final class AcceptCguTest extends ApplicationTestCase
{
    private User $user;
    private AcceptCgu $acceptCgu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->acceptCgu = self::$container->get(AcceptCgu::class);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);

        self::$container->get(UserRepository::class)->save($this->user);
    }

    /**
     * @dataProvider userTestDataProvider
     */
    public function testAcceptCgu(?string $id = null, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $user = $this->acceptCgu->acceptCgu();

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertTrue($user->getCguAccepted());
    }

    /**
     * @return mixed[]
     */
    public function userTestDataProvider(): array
    {
        return [
            'Another Valid User' => [],
        ];
    }
}
