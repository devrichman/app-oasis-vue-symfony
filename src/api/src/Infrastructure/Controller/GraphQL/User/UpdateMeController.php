<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\UpdateMe;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Infrastructure\Security\SerializableUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Bundle\Types\BasicUser;
use function is_object;

final class UpdateMeController extends AbstractController
{
    private UpdateMe $updateMe;
    private TokenStorageInterface $tokenStorage;

    public function __construct(UpdateMe $updateMe, TokenStorageInterface $tokenStorage)
    {
        $this->updateMe = $updateMe;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws Exist
     * @throws InvalidStringValue
     * @throws NotFound
     *
     * @Mutation()
     * @Logged()
     */
    public function updateMe(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $profilePictureId = null
    ): UserInterface {
        $this->updateMe->updateMe(
            $firstName,
            $lastName,
            $email,
            $phone,
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            $profilePictureId,
        );

        $token = $this->tokenStorage->getToken();

        if (! is_object($token)) {
            throw new AuthenticationCredentialsNotFoundException('token not found');
        }

        $user = $token->getUser();

        if (! $user instanceof SerializableUser) {
            throw new AuthenticationCredentialsNotFoundException('token not found');
        }

        $user->updateSerializableUser($firstName, $lastName, $function, $phone, $address, $civility, $profilePictureId);

        if (! $user instanceof UserInterface) {
            // getUser() can be an object with a toString or a string
            $userName = (string) $user;
            $user = new BasicUser($userName);
        }

        return $user;
    }
}
