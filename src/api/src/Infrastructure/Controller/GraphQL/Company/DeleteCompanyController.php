<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\DeleteCompany;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class DeleteCompanyController extends AbstractController
{
    private DeleteCompany $deleteCompany;

    public function __construct(DeleteCompany $deleteCompany)
    {
        $this->deleteCompany = $deleteCompany;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_DELETE_COMPANY")
     */
    public function deleteCompany(
        string $id
    ): Company {
        return $this->deleteCompany->delete($id);
    }
}
