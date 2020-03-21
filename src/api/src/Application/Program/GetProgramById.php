<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramRepository;

final class GetProgramById
{
    private ProgramRepository $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $programId): Program
    {
        return $this->programRepository->mustFindOneById($programId);
    }
}
