<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Model\User;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use function array_unique;

/**
 * @Type
 */
final class SerializableUser implements UserInterface
{
    private string $id;
    private string $firstName;
    private string $lastName;
    private ?DateTimeImmutable $seniorityDate;
    private string $email;
    private ?string $profilePictureId;
    private string $password;
    private ?string $linkedin;
    private ?string $address;
    private string $civility;
    private ?string $function;
    /** @var string[] */
    private array $rights;
    private string $phone;
    private string $type;
    private bool $cguAccepted;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->address = $user->getAddress();
        $this->seniorityDate = $user->getSeniorityDate();
        $this->email = $user->getEmail();
        $this->civility = $user->getCivility();
        $this->linkedin = $user->getLinkedin();
        $this->function = $user->getFunction();
        if ($user->getPassword() === null) {
            throw new RuntimeException('password should not be null');
        }
        $this->password = $user->getPassword();

        $this->rights = [];
        foreach ($user->getRoles() as $role) {
            foreach ($role->getRights() as $right) {
                $this->rights[] = $right->getCode();
            }
        }

        $this->rights = array_unique($this->rights);
        $this->phone = $user->getPhone();
        $this->profilePictureId = $user->getProfilePicture() !==null ? $user->getProfilePicture()->getId() : null;
        $this->type = $user->getType()->getId();
        $this->cguAccepted = $user->getCguAccepted();
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->rights;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @Field
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @Field
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @Field
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @Field
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     *
     * @Field
     */
    public function getRights(): array
    {
        return $this->rights;
    }

    /**
     * @Field
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @Field
     */
    public function getProfilePictureId(): ?string
    {
        return $this->profilePictureId;
    }

    /**
     * @Field
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @Field
     */
    public function getCguAccepted(): bool
    {
        return $this->cguAccepted;
    }

    /**
     * @Field
     */
    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @Field
     */
    public function getSeniorityDate(): ?DateTimeImmutable
    {
        return $this->seniorityDate;
    }

    /**
     * @Field
     */
    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    /**
     * @Field
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @Field
     */
    public function getCivility(): string
    {
        return $this->civility;
    }

    public function updateSerializableUser(string $firstname, string $lastname, ?string $function, string $phone, ?string $address, string $civility, ?string $profilePictureId): void
    {
        $this->firstName = $firstname;
        $this->lastName = $lastname;
        $this->function = $function;
        $this->phone = $phone;
        $this->address = $address;
        $this->civility = $civility;
        $this->profilePictureId = $profilePictureId;
    }
}
