<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\NotFound;
use App\Domain\Exception\UnableDelete;
use App\Domain\Model\ProgramModel;
use App\Domain\Repository\ProgramModelRepository;

final class DeleteProgramModel
{
    private ProgramModelRepository $programModelRepository;

    public function __construct(ProgramModelRepository $programModelRepository)
    {
        $this->programModelRepository = $programModelRepository;
    }

    /**
     * @throws NotFound
     * @throws UnableDelete
     */
    public function delete(string $id): ProgramModel
    {
        $programModel = $this->programModelRepository->mustFindOneById($id);
        UnableDelete::mustNotHavePrograms($programModel);
        $programModel->setDeleted(true);
        $this->programModelRepository->save($programModel);

        return $programModel;
    }
}
