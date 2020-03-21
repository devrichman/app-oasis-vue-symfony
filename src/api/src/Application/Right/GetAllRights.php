<?php

declare(strict_types=1);

namespace App\Application\Right;

use App\Domain\Model\Right;
use App\Domain\Repository\RightRepository;

final class GetAllRights
{
    private RightRepository $rightRepository;

    public function __construct(RightRepository $rightRepository)
    {
        $this->rightRepository = $rightRepository;
    }

    /**
     * @return Right[]
     */
    public function getAll(): array
    {
        /** @var Right[] $rights */
        $rights = $this->rightRepository->findByFilters()->toArray();

        return $rights;
    }
}
