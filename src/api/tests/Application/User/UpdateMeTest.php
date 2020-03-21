<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\UpdateMe;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\Role;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Infrastructure\Security\SerializableUser;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use function array_map;
use function preg_quote;

final class UpdateMeTest extends ApplicationTestCase
{
    /** @var Role[] $roles */
    private array $roles;
    private User $coach;
    private Company $company;
    private FileDescriptor $profilePicture;
    private UpdateMe $updateMyProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->coach = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->company = new Company($this->faker->company, $this->faker->companySuffix);
        $this->profilePicture = new FileDescriptor($this->faker->name, $this->faker->numberBetween(1500, 25000), $this->faker->name);
        $this->roles = [new Role($this->faker->name), new Role($this->faker->name)];
        $this->updateMyProfile = self::$container->get(UpdateMe::class);

        array_map(static fn(Role $role) => self::$container->get(RoleRepository::class)->save($role), $this->roles);
        self::$container->get(UserRepository::class)->save($this->coach);
        self::$container->get(CompanyRepository::class)->save($this->company);
        self::$container->get(FileDescriptorRepository::class)->save($this->profilePicture);
    }

    /**
     * @param mixed[] $updateData
     *
     * @dataProvider updateMeDataProvider
     */
    public function testUpdateMe(array $updateData, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $firstName = $updateData['firstName'];
        $lastName = $updateData['lastName'];
        $email = $updateData['email'];
        $phone = $updateData['phone'];
        $profilePictureId = $updateData['profilePictureId'] ?? $this->profilePicture->getId();
        $address = $address ?? $this->faker->address;
        $linkedin = $linkedin ?? $this->faker->url;
        $function = $function ?? $this->faker->jobTitle;
        $previousFunction = $previousFunction ?? $this->faker->jobTitle;
        $civility = $civility ?? CivilityEnum::MADAM_CODE;

        $this->updateMyProfile->updateMe(
            $firstName,
            $lastName,
            $email,
            $phone,
            $civility,
            $address,
            $linkedin,
            $function,
            null,
            $previousFunction,
            $profilePictureId,
        );

        $user = $this->loggedUser;

        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($phone, $user->getPhone());
        $this->assertEquals($address, $user->getAddress());
        $this->assertEquals($linkedin, $user->getLinkedin());
        $this->assertEquals($function, $user->getFunction());
        $this->assertEquals($previousFunction, $user->getPreviousFunction());
        $this->assertEquals($civility, $user->getCivility());
    }

    public function testUpdateMeWithSerializableUser(): void
    {
        $logged = $this->loggedUser;

        $user = new SerializableUser($logged);

        if (! $user instanceof SerializableUser) {
            throw new AuthenticationCredentialsNotFoundException('token not found');
        }

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $this->faker->email;
        $phone = $this->faker->phoneNumber;
        $profilePictureId = $this->profilePicture->getId();
        $address = $this->faker->address;
        $function = $this->faker->jobTitle;
        $civility = CivilityEnum::MADAM_CODE;

        $user->updateSerializableUser(
            $firstName,
            $lastName,
            $function,
            $phone,
            $address,
            $civility,
            $profilePictureId,
        );

        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($phone, $user->getPhone());
        $this->assertEquals($address, $user->getAddress());
        $this->assertEquals($function, $user->getFunction());
        $this->assertEquals($civility, $user->getCivility());
    }

    /**
     * @return mixed[]
     */
    public function updateMeDataProvider(): array
    {
        return [
            'Update simple' => [
                ['firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
            ],
            'Failure because invalid profile picture' => [
                ['profilePictureId' => Uuid::uuid1()->toString(), 'firstName' => 'John', 'lastName' => 'Smith', 'email' => 'random@email.com', 'phone' => '+33123456789'],
                NotFound::class,
                FileDescriptor::class,
            ],
        ];
    }
}
