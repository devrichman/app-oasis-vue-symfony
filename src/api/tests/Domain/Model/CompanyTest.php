<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\Company;
use App\Tests\Domain\DomainTestCase;

final class CompanyTest extends DomainTestCase
{
    public function testSetCompanyName(): void
    {
        $company = new Company($this->faker->name, $this->faker->firstName);

        $name = $this->faker->name;
        $company->setName($name);
        $this->assertEquals($name, $company->getName());

        $this->expectException(InvalidStringValue::class);
        $company->setName('');

        $this->expectException(InvalidStringValue::class);
        $company->setName($this->faker->text(256));
    }

    public function testSetCompanyCode(): void
    {
        $company = new Company($this->faker->name, $this->faker->firstName);

        $code = $this->faker->firstName;
        $company->setCode($code);
        $this->assertEquals($code, $company->getCode());

        $this->expectException(InvalidStringValue::class);
        $company->setCode('');

        $this->expectException(InvalidStringValue::class);
        $company->setCode($this->faker->text(256));
    }
}
