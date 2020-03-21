<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\CreateCompany;
use App\Domain\Exception\Exist;
use App\Domain\Model\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateCompanyController extends AbstractController
{
    private CreateCompany $createCompany;

    public function __construct(CreateCompany $createCompany)
    {
        $this->createCompany = $createCompany;
    }

    /**
     * @throws Exist
     *
     * @Logged
     * @Mutation
     * @Right("ROLE_CREATE_COMPANY")
     */
    public function createCompany(string $name, ?string $salesforceLink = null): Company
    {
        return $this->createCompany->create($name, $salesforceLink);
    }
}
