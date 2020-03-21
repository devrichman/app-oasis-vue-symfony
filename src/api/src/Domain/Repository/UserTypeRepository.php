<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\UserType;
use TheCodingMachine\TDBM\ResultIterator;

interface UserTypeRepository
{
    /**
     * @return ResultIterator|UserType[]
     */
    public function findAll(): ResultIterator;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): UserType;
}
