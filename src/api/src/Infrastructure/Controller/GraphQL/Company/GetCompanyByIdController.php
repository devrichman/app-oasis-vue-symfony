<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\GetCompanyById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetCompanyByIdController extends AbstractController
{
    private GetCompanyById $getCompanyById;

    public function __construct(GetCompanyById $getCompanyById)
    {
        $this->getCompanyById = $getCompanyById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getCompanyById(string $id): Company
    {
        return $this->getCompanyById->get($id);
    }
}
