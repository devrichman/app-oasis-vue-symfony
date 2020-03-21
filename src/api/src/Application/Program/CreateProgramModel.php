<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Model\ProgramModel;
use App\Domain\Repository\ProgramModelRepository;

final class CreateProgramModel
{
    private ProgramModelRepository $programModelRepository;

    public function __construct(ProgramModelRepository $programModelRepository)
    {
        $this->programModelRepository = $programModelRepository;
    }

    public function create(string $name, string $description): ProgramModel
    {
        $programModel = new ProgramModel($name, $description);

        $this->programModelRepository->save($programModel);

        return $programModel;
    }
}
