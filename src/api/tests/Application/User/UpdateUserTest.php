<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\UpdateUser;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\UserTypeEnum;
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

final class UpdateUserTest extends ApplicationTestCase
{
    /** @var Role[] $roles */
    private array $roles;
    private User $coach;
    private Company $company;
    private FileDescriptor $profilePicture;
    private UserType $userType;

    protected function setUp(): void
    {
        parent::setUp();

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
     * @param mixed[] $updateData
     *
     * @dataProvider updateUserDataProvider
     */
    public function testUpdateUser(array $updateData, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $updateUser = self::$container->get(UpdateUser::class);

        $firstName = $updateData['firstName'];
        $lastName = $updateData['lastName'];
        $email = $updateData['email'];
        $phone = $updateData['phone'];
        $coachId = $updateData['coachId'] ?? $this->coach->getId();
        $companyId = $updateData['companyId'] ?? $this->company->getId();
        $profilePictureId = $updateData['profilePictureId'] ?? $this->profilePicture->getId();
        $userType = $updateData['typeId'] ?? UserTypeEnum::ADMINISTRATOR;
        $status = $updateData['status'] ?? true;
        $address = $address ?? $this->faker->address;
        $linkedin = $linkedin ?? $this->faker->url;
        $function = $function ?? $this->faker->jobTitle;
        $previousFunction = $previousFunction ?? $this->faker->jobTitle;
        $civility = $civility ?? CivilityEnum::MISTER_CODE;
        $roles = $roles ?? array_map(static fn(Role $role) => $role->getId(), $this->roles);

        $updateUser->updateUser(
            $updateData['id'] ?? $this->loggedUser->getId(),
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
            null,
            $previousFunction,
            $companyId,
            $coachId,
            $profilePictureId,
            $status
        );

        if (empty($exceptionClass)) {
            $this->assertEquals($this->loggedUser->getFirstName(), $firstName);
            $this->assertEquals($this->loggedUser->getLastName(), $lastName);
            $this->assertEquals($this->loggedUser->getPhone(), $phone);
            $this->assertEquals($this->loggedUser->getCoach()->getId(), $coachId);
            $this->assertEquals($this->loggedUser->getCompany()->getId(), $companyId);
            $this->assertEquals($this->loggedUser->getStatus(), $status);
            if ($profilePictureId === '') {
                $this->assertNull($this->loggedUser->getProfilePicture());
            } else {
                $this->assertEquals($this->loggedUser->getProfilePicture()->getId(), $profilePictureId);
            }
            $userRoles = $this->loggedUser->getRolesByUsersRoles();
            $this->assertEquals(count($userRoles), count($this->roles));
            foreach ($roles as $role) {
                $this->assertContains($role, array_map(static fn(Role $role) => $role->getId(), $userRoles));
            }
        } elseif (! isset($updateData['id'])) {
            $this->assertNull($this->loggedUser->getUpdatedBy());
        }
    }

    /**
     * @return mixed[]
     */
    public function updateUserDataProvider(): array
    {
        return [
            'Update simple' => [
                ['firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
            ],
            'Unset profile picture' => [
                ['firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789', 'profilePictureId' => '', 'status' => false],
            ],
            'Failure because invalid user' => [
                ['id' => Uuid::uuid1()->toString(), 'firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
                NotFound::class,
                User::class,
            ],
            'Failure because invalid coach' => [
                ['coachId' => Uuid::uuid1()->toString(), 'firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
                NotFound::class,
                User::class,
            ],
            'Failure because invalid profile picture' => [
                ['profilePictureId' => Uuid::uuid1()->toString(), 'firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
                NotFound::class,
                FileDescriptor::class,
            ],
            'Failure because invalid company' => [
                ['companyId' => Uuid::uuid1()->toString(), 'firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
                NotFound::class,
                Company::class,
            ],
        ];
    }
}
