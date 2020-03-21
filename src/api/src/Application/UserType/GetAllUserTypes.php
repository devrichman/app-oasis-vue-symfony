<?php

declare(strict_types=1);

namespace App\Application\UserType;

use App\Domain\Model\UserType;
use App\Domain\Repository\UserTypeRepository;

final class GetAllUserTypes
{
    private UserTypeRepository $userTypeRepository;

    public function __construct(UserTypeRepository $userTypeRepository)
    {
        $this->userTypeRepository = $userTypeRepository;
    }

    /**
     * @return UserType[]
     */
    public function getAll(): array
    {
        /** @var UserType[] $userTypes */
        $userTypes = $this->userTypeRepository->findAll()->toArray();

        return $userTypes;
    }
}
