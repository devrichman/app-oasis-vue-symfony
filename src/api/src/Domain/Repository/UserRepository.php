<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use TheCodingMachine\TDBM\ResultIterator;

interface UserRepository
{
    public function save(User $user): void;

    public function saveNoLog(User $user): void;

    public function findOneByEmail(string $email): ?User;

    public function checkEmailUnique(string $email, ?string $userId = null): bool;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): User;

    /**
     * @return User[]|ResultIterator
     */
    public function findByFilters(?string $search, ?string $company, bool $coachesOnly, ?string $role, ?string $companyId, ?string $sortColumn, ?string $sortDirection): ResultIterator;

    public function getLoggedUser(): User;
}
