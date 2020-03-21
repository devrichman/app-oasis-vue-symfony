<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\ProgramModel;
use App\Tests\Domain\DomainTestCase;

final class ProgramModelTest extends DomainTestCase
{
    public function testSetProgramName(): void
    {
        $program = new ProgramModel($this->faker->name, $this->faker->text);

        $name = $this->faker->name;
        $program->setName($name);
        $this->assertEquals($name, $program->getName());

        $this->expectException(InvalidStringValue::class);
        $program->setName('');

        $this->expectException(InvalidStringValue::class);
        $program->setName($this->faker->text(256));
    }

    public function testSetProgramDescription(): void
    {
        $program = new ProgramModel($this->faker->name, $this->faker->text);
        $description = $this->faker->text;
        $program->setDescription($description);
        $this->assertEquals($description, $program->getDescription());
        $this->expectException(InvalidStringValue::class);
        $program->setDescription('');
    }
}
