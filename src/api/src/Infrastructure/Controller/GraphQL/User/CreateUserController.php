<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\CreateUser;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidRight;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateUserController extends AbstractController
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws NotFound
     * @throws Exist
     * @throws InvalidStringValue
     * @throws InvalidArrayValue
     * @throws InvalidRight
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_USER")
     */
    public function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $typeId,
        array $roleIds,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $companyId = null,
        ?string $coachId = null,
        ?string $profilePictureId = null,
        bool $status = true
    ): User {
        return $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $phone,
            $typeId,
            $roleIds,
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            $companyId,
            $coachId,
            $profilePictureId,
            $status,
        );
    }
}
