<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Exception\Exist;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;
use Safe\DateTimeImmutable;
use function explode;
use function intval;

final class CreateCompany
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @throws Exist
     */
    public function create(string $name, ?string $salesforceLink = null): Company
    {
        $companyNameCheck = $this->companyRepository->findOneByName($name);
        if ($companyNameCheck !== null) {
            throw new Exist(Company::class, [], true);
        }

        // Code generation
        $company = $this->companyRepository->findByFilters(null, 'createdAt', 'DESC')->first();
        $count = 1;
        $date = new DateTimeImmutable();
        $code = 'ENT' . $date->format('Y') . $date->format('dm') . '_' . $count;
        if ($company) {
            $countLast = explode('_', $company->getCode())[1];
            $count = intval($countLast) + 1;
            $code = 'ENT' . $date->format('Y') . $date->format('dm') . '_' . $count;
        }

        $company = new Company($name, $code);
        if ($salesforceLink !== null) {
            $company->setSalesforceLink($salesforceLink);
        }

        $this->companyRepository->save($company);

        return $company;
    }
}
