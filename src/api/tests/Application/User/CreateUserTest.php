<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\CreateUser;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\Role;
use App\Domain\Model\User;
use App\Domain\Model\UserType;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function array_map;
use function count;
use function preg_quote;

final class CreateUserTest extends ApplicationTestCase
{
    private CreateUser $createUser;
    private User $coach;
    private Company $company;
    private FileDescriptor $profilePicture;

    /** @var Role[] $roles */
    private array $roles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUser = self::$container->get(CreateUser::class);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->coach = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->company = new Company($this->faker->company, $this->faker->companySuffix);
        $this->profilePicture = new FileDescriptor($this->faker->name, $this->faker->numberBetween(1500, 25000), $this->faker->name);
        $this->roles = [new Role($this->faker->name), new Role($this->faker->name)];

        array_map(static fn(Role $role) => self::$container->get(RoleRepository::class)->save($role), $this->roles);
        self::$container->get(UserRepository::class)->save($this->coach);
        self::$container->get(CompanyRepository::class)->save($this->company);
        self::$container->get(FileDescriptorRepository::class)->save($this->profilePicture);
    }

    /**
     * @param string[] $roles
     *
     * @dataProvider userTestDataProvider
     */
    public function testCreate(?string $email = null, ?string $coach = null, ?string $userType = null, ?string $company = null, ?array $roles = null, ?string $profilePicture = null, ?bool $status = true, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $email ?? $this->faker->email;
        $phone = $this->faker->phoneNumber;
        $coach = $coach ?? $this->coach->getId();
        $userType = $userType ?? UserTypeEnum::ADMINISTRATOR;
        $company = $company ?? $this->company->getId();
        $profilePicture = $profilePicture ?? $this->profilePicture->getId();
        $roles = $roles ?? array_map(static fn(Role $role) => $role->getId(), $this->roles);
        $address = $address ?? $this->faker->address;
        $linkedin = $linkedin ?? $this->faker->url;
        $function = $function ?? $this->faker->jobTitle;
        $previousFunction = $previousFunction ?? $this->faker->jobTitle;
        $civility = $civility ?? CivilityEnum::MISTER_CODE;
        $seniorityDate = $this->faker->dateTIme->format('c');

        $user = $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $phone,
            $userType,
            $roles,
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            $company,
            $coach,
            $profilePicture,
            $status
        );

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($phone, $user->getPhone());
        $this->assertEquals($userType, $user->getType()->getId());
        $this->assertEquals($profilePicture, $user->getProfilePicture()->getId());
        $this->assertEquals(CivilityEnum::MISTER_CODE, $user->getCivility());
        $this->assertFalse($user->getCguAccepted());
        $this->assertEquals($company, $user->getCompany()->getId());

        if ($userType === UserTypeEnum::CANDIDATE) {
            $this->assertEquals($coach, $user->getCoach()->getId());
        } else {
            $this->assertNull($user->getCoach());
        }

        $userRoles = $user->getRolesByUsersRoles();
        $this->assertEquals(count($userRoles), count($this->roles));
        foreach ($roles as $role) {
            $this->assertContains($role, array_map(static fn(Role $role) => $role->getId(), $userRoles));
        }
    }

    /**
     * @param string[] $users
     *
     * @dataProvider duplicateUserDataProvider
     */
    public function testCreateDuplicateUser(array $users): void
    {
        $this->expectException(Exist::class);
        foreach ($users as $user) {
            $this->createUser->create(
                $this->faker->firstName,
                $this->faker->lastName,
                $user,
                $this->faker->phoneNumber,
                UserTypeEnum::ADMINISTRATOR,
                array_map(static fn(Role $role) => $role->getId(), $this->roles),
                CivilityEnum::MISTER_CODE,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->coach->getId(),
                $this->profilePicture->getId(),
            );
        }
    }

    /**
     * @return mixed[]
     */
    public function userTestDataProvider(): array
    {
        return [
            'Another Valid User' => ['email' => 'random.email@gmail.com'],
            'A valid candidate' => [
                'email' => 'random.email@gmail.com',
                'userType' => UserTypeEnum::CANDIDATE,
            ],
            'Failure because missing coach' => [
                'email' => null,
                'coach' => Uuid::uuid1()->toString(),
                'userType' => UserTypeEnum::CANDIDATE,
                'company' => null,
                'roles' => null,
                'profilePicture' => null,
                'status' => true,
                'exceptionClass' => NotFound::class,
                'exceptionContains' => User::class,
            ],
            'Failure because missing user type' => [
                'email' => null,
                'coach' => null,
                'userType' => Uuid::uuid1()->toString(),
                'company' => null,
                'roles' => null,
                'profilePicture' => null,
                'status' => false,
                'exceptionClass' => NotFound::class,
                'exceptionContains' => UserType::class,
            ],
            'Failure because missing company' => [
                'email' => null,
                'coach' => null,
                'userType' => UserTypeEnum::CANDIDATE,
                'company' => Uuid::uuid1()->toString(),
                'roles' => null,
                'profilePicture' => null,
                'status' => true,
                'exceptionClass' => NotFound::class,
                'exceptionContains' => Company::class,
            ],
            'Failure because missing role' => [
                'email' => null,
                'coach' => null,
                'userType' => null,
                'company' => null,
                'roles' => [Uuid::uuid1()->toString()],
                'profilePicture' => null,
                'status' => true,
                'exceptionClass' => NotFound::class,
                'exceptionContains' => Role::class,
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public function duplicateUserDataProvider(): array
    {
        return [
            [['random.email@gmail.com', 'random.email@gmail.com']],
        ];
    }
}
