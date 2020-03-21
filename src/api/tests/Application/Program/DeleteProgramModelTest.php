<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\DeleteProgramModel;
use App\Domain\Model\ProgramModel;
use App\Domain\Repository\ProgramModelRepository;
use App\Tests\Application\ApplicationTestCase;

class DeleteProgramModelTest extends ApplicationTestCase
{
    protected DeleteProgramModel $deleteProgramModel;
    protected ProgramModel $programModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteProgramModel = self::$container->get(DeleteProgramModel::class);
        $programModelRepository = self::$container->get(ProgramModelRepository::class);

        $this->programModel = new ProgramModel($this->faker->name, $this->faker->text);
        $programModelRepository->save($this->programModel);
    }

    public function testDeleteProgramModel(): void
    {
        $programModelId = $this->programModel->getId();
        $this->deleteProgramModel->delete($programModelId);
        $this->assertTrue($this->programModel->getDeleted());
    }
}
