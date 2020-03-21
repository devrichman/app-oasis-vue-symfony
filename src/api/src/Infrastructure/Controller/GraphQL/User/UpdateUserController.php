<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\UpdateUser;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateUserController extends AbstractController
{
    private UpdateUser $updateUser;

    public function __construct(UpdateUser $updateUser)
    {
        $this->updateUser = $updateUser;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws InvalidStringValue
     * @throws NotFound
     * @throws Exist
     *
     * @Mutation()
     * @Logged()
     * @Right("ROLE_UPDATE_USER")
     */
    public function updateUser(
        string $id,
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
        return $this->updateUser->updateUser(
            $id,
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
