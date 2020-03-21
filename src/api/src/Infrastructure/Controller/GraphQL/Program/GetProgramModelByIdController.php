<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\GetProgramModelById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetProgramModelByIdController extends AbstractController
{
    private GetProgramModelById $getProgramModelById;

    public function __construct(GetProgramModelById $getProgramModelById)
    {
        $this->getProgramModelById = $getProgramModelById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getProgramModelById(string $programModelId): ProgramModel
    {
        return $this->getProgramModelById->get($programModelId);
    }
}
