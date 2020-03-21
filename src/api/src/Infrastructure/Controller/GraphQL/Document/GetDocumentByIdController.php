<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\GetDocumentById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetDocumentByIdController extends AbstractController
{
    private GetDocumentById $getDocumentById;

    public function __construct(GetDocumentById $getDocumentById)
    {
        $this->getDocumentById = $getDocumentById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getDocumentById(string $id): Document
    {
        return $this->getDocumentById->get($id);
    }
}
