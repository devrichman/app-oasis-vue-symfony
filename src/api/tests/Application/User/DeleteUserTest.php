<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\DeleteUser;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function preg_quote;

final class DeleteUserTest extends ApplicationTestCase
{
    private DeleteUser $deleteUser;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deleteUser = self::$container->get(DeleteUser::class);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->user->setDeleted(false);

        self::$container->get(UserRepository::class)->save($this->user);
    }

    /**
     * @dataProvider userTestDataProvider
     */
    public function testDelete(?string $id = null, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $id = $id ?? $this->user->getId();

        $user = $this->deleteUser->deleteUser($id);

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertTrue($user->getDeleted());
    }

    /**
     * @return mixed[]
     */
    public function userTestDataProvider(): array
    {
        return [
            'Another Valid User' => [],
            'Failure because missing user' => [
                'id' => Uuid::uuid1()->toString(),
                'exceptionClass' => NotFound::class,
                'exceptionContains' => User::class,
            ],
        ];
    }
}
