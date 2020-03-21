<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\GetProgramById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetProgramByIdController extends AbstractController
{
    private GetProgramById $getProgramById;

    public function __construct(GetProgramById $getProgramById)
    {
        $this->getProgramById = $getProgramById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getProgramById(string $programId): Program
    {
        return $this->getProgramById->get($programId);
    }
}
