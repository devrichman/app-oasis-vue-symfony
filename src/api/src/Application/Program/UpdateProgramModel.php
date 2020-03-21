<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramModel;
use App\Domain\Repository\ProgramModelRepository;

final class UpdateProgramModel
{
    private ProgramModelRepository $programModelRepository;

    public function __construct(ProgramModelRepository $programModelRepository)
    {
        $this->programModelRepository = $programModelRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     */
    public function update(string $id, string $name, string $description): ProgramModel
    {
        $programModel = $this->programModelRepository->mustFindOneById($id);
        $programModel->setName($name);
        $programModel->setDescription($description);

        $this->programModelRepository->save($programModel);

        return $programModel;
    }
}
