<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\GetAllEventModels;
use App\Domain\Model\EventModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllEventModelsController extends AbstractController
{
    private GetAllEventModels $getAllEventModels;

    public function __construct(GetAllEventModels $getAllEventModels)
    {
        $this->getAllEventModels = $getAllEventModels;
    }

    /**
     * @return ResultIterator|EventModel[]
     *
     * @Query
     * @Logged
     */
    public function getAllEventModels(?string $search = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        /** @var ResultIterator|EventModel[] $result */
        $result = $this->getAllEventModels->getAll($search, $sortColumn, $sortDirection);

        return $result;
    }
}
