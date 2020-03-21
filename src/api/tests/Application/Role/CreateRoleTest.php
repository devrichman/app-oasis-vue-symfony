<?php

declare(strict_types=1);

namespace App\Tests\Application\Role;

use App\Application\Role\CreateRole;
use App\Domain\Enum\RightEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Right;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use function array_map;
use function count;
use function preg_quote;

class CreateRoleTest extends ApplicationTestCase
{
    protected CreateRole $createRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRole = self::$container->get(CreateRole::class);
    }

    /**
     * @param string[] $rights
     *
     * @dataProvider createRoleTestData
     */
    public function testCreateRole(string $name, string $description, array $rights, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $role = $this->createRole->create($name, $description, $rights);

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($role->getName(), $name);
        $this->assertEquals($role->getDescription(), $description);
        $this->assertEquals(count($role->getRights()), count($rights));
        foreach ($rights as $right) {
            $this->assertContains($right, array_map(static fn(Right $right) => $right->getCode(), $role->getRights()));
        }
    }

    /**
     * @return mixed[]
     */
    public function createRoleTestData(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            [$faker->name, $faker->text(255), [RightEnum::CREATE_USER_CODE]],
            [$faker->name, $faker->text(255), []],
            [RoleEnum::ADMINISTRATEUR_NAME, $faker->text(255), [RightEnum::CREATE_USER_CODE], Exist::class],
            [$faker->name, $faker->text(255), ['TEST_INVALID_RIGHT'], NotFound::class, Right::class],
        ];
    }
}
