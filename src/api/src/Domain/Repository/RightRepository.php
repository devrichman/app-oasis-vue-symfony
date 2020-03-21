<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Right;
use TheCodingMachine\TDBM\ResultIterator;

interface RightRepository
{
    /**
     * @throws NotFound
     */
    public function mustFindOneByCode(string $code): Right;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Right;

    public function findAll(): ResultIterator;

    /**
     * @return Right[]|ResultIterator
     */
    public function findByFilters(): ResultIterator;
}
