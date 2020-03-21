<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramModel;
use App\Domain\Repository\ProgramModelRepository;

final class GetProgramModelById
{
    private ProgramModelRepository $programModelRepository;

    public function __construct(ProgramModelRepository $programModelRepository)
    {
        $this->programModelRepository = $programModelRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $programModelId): ProgramModel
    {
        return $this->programModelRepository->mustFindOneById($programModelId);
    }
}
