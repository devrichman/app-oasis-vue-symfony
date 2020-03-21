<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;

final class UpdateCompany
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws Exist
     */
    public function update(string $id, string $name, ?string $salesforceLink = null): Company
    {
        $company = $this->companyRepository->mustFindOneById($id);

        if ($name !== $company->getName()) {
            $companyNameCheck = $this->companyRepository->findOneByName($name);
            if ($companyNameCheck !== null) {
                throw new Exist(Company::class, [], true);
            }
        }

        $company->setName($name);
        $company->setSalesforceLink($salesforceLink === '' ? null : $salesforceLink);

        $this->companyRepository->save($company);

        return $company;
    }
}
