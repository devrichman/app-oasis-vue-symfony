<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;

class DeleteCompany
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $documentRepository)
    {
        $this->companyRepository = $documentRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $id): Company
    {
        $company = $this->companyRepository->mustFindOneById($id);
        $company->setDeleted(true);

        $this->companyRepository->save($company);

        return $company;
    }
}
