<?php

declare(strict_types=1);

namespace App\Tests\Application\Company;

use App\Application\Company\CreateCompany;
use App\Domain\Exception\Exist;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use function preg_quote;

class CreateCompanyTest extends ApplicationTestCase
{
    protected CreateCompany $createCompany;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createCompany = self::$container->get(CreateCompany::class);
    }

    /**
     * @dataProvider createCompanyDataProvider
     */
    public function testCreateCompany(string $name, ?string $salesforceLink = null, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $company = $this->createCompany->create($name, $salesforceLink);

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($company->getName(), $name);
        if ($salesforceLink === null) {
            $this->assertNull($company->getSalesforceLink());
        } else {
            $this->assertEquals($company->getSalesforceLink(), $salesforceLink);
        }
    }

    public function testCreateCompanyDuplicate(): void
    {
        $this->expectException(Exist::class);
        $this->createCompany->create('Company 1', 'COMPANY-1');
        $this->createCompany->create('Company 1', 'COMPANY-2');

        $this->expectException(Exist::class);
        $this->createCompany->create('Company 3', 'COMPANY-3');
        $this->createCompany->create('Company 4', 'COMPANY-3');
    }

    /**
     * @return mixed[]
     */
    public function createCompanyDataProvider(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            [$faker->company, null],
            [$faker->company, $faker->url],
        ];
    }
}
