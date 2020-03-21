<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllUsers
{
    private UserRepository $userRepository;
    private CompanyRepository $companyRepository;

    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    public function getAll(?string $search, ?string $companyName = null, bool $coachesOnly = false, ?string $roleId = null, ?string $companyId = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        return $this->userRepository->findByFilters($search, $companyName, $coachesOnly, $roleId, $companyId, $sortColumn, $sortDirection);
    }
}
