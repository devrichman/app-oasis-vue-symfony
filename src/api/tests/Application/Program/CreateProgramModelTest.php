<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\CreateProgramModel;
use App\Tests\Application\ApplicationTestCase;

class CreateProgramModelTest extends ApplicationTestCase
{
    protected CreateProgramModel $createProgramModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createProgramModel = self::$container->get(CreateProgramModel::class);
    }

    public function testCreateProgramModel(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;

        $program = $this->createProgramModel->create(
            $name,
            $description,
        );

        $this->assertEquals($name, $program->getName());
        $this->assertEquals($description, $program->getDescription());
    }
}
