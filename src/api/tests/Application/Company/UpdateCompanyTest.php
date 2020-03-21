<?php

declare(strict_types=1);

namespace App\Tests\Application\Company;

use App\Application\Company\UpdateCompany;
use App\Domain\Exception\Exist;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class UpdateCompanyTest extends ApplicationTestCase
{
    protected UpdateCompany $updateCompany;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateCompany = self::$container->get(UpdateCompany::class);

        $this->company = new Company($this->faker->company, $this->faker->company);
        $this->company->setSalesforceLink($this->faker->url);
        self::$container->get(CompanyRepository::class)->save($this->company);
    }

    /**
     * @dataProvider updateCompanyDataProvider
     */
    public function testUpdateCompany(?string $id, string $name, ?string $salesforceLink = null, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $this->updateCompany->update($id ?? $this->company->getId(), $name, $salesforceLink);

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($this->company->getName(), $name);
        if ($salesforceLink === '') {
            $this->assertNull($this->company->getSalesforceLink());
        } else {
            $this->assertEquals($this->company->getSalesforceLink(), $salesforceLink);
        }
    }

    public function testUpdateCompanyDuplicate(): void
    {
        $company = new Company($this->faker->company, $this->faker->company);
        $company->setSalesforceLink($this->faker->url);
        self::$container->get(CompanyRepository::class)->save($company);

        $this->expectException(Exist::class);
        $this->updateCompany->update($company->getId(), $this->company->getName(), 'COMPANY-2');

        $this->expectException(Exist::class);
        $this->updateCompany->update($company->getId(), 'Company 4', $this->company->getCode());
    }

    /**
     * @return mixed[]
     */
    public function updateCompanyDataProvider(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            [null, $faker->company],
            [null, $faker->company, $faker->url],
            [Uuid::uuid1()->toString(), $faker->company, $faker->url, NotFound::class, Company::class],
        ];
    }
}
