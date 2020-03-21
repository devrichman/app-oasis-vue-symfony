<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

abstract class DomainTestCase extends TestCase
{
    protected Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create('fr_FR');
        parent::setUp();
    }
}
