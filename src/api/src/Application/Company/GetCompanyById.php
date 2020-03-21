<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;

final class GetCompanyById
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id): Company
    {
        return $this->companyRepository->mustFindOneById($id);
    }
}
