<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\GetAllCompanies;
use App\Domain\Model\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllCompaniesController extends AbstractController
{
    private GetAllCompanies $getAllCompanies;

    public function __construct(GetAllCompanies $getAllCompanies)
    {
        $this->getAllCompanies = $getAllCompanies;
    }

    /**
     * @return ResultIterator|Company[]
     *
     * @Logged
     * @Query
     */
    public function getAllCompanies(?string $search = null, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator
    {
        /** @var ResultIterator|Company[] $result */
        $result = $this->getAllCompanies->getAll($search, $sortColumn, $sortDirection);

        return $result;
    }
}
