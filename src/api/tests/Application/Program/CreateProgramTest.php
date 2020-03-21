<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\CreateProgram;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramModel;
use App\Domain\Model\User;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function array_map;
use function preg_quote;

class CreateProgramTest extends ApplicationTestCase
{
    protected CreateProgram $createProgram;
    protected ProgramModel $programModel;

    /** @var User[] $users */
    protected array $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createProgram = self::$container->get(CreateProgram::class);

        $this->programModel = new ProgramModel($this->faker->name, $this->faker->text);
        self::$container->get(ProgramModelRepository::class)->save($this->programModel);

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::COACH);
        $user = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        self::$container->get(UserRepository::class)->save($user);

        $this->users[] = $user;
    }

    public function testCreateProgram(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = ProgramTypeEnum::INDIVIDUAL;
        $userIds = array_map(static fn(User $user) => $user->getId(), $this->users);

        $program = $this->createProgram->create(
            $name,
            $description,
            $type,
            $userIds,
            $userIds[0],
            $this->programModel->getId(),
        );

        $this->assertEquals($name, $program->getName());
        $this->assertEquals($description, $program->getDescription());
        $this->assertEquals($type, $program->getType());
        $this->assertEquals($userIds[0], $program->getCoach()->getId());
        $this->assertEquals($this->programModel->getId(), $program->getProgramModel()->getId());
    }

    public function testInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(ProgramModel::class) . '/');
        $this->createProgram->create(
            $this->faker->name,
            $this->faker->text,
            ProgramTypeEnum::INDIVIDUAL,
            array_map(static fn(User $user) => $user->getId(), $this->users),
            null,
            Uuid::uuid1()->toString(),
        );
    }

    public function testInvalidCoach(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(User::class) . '/');
        $this->createProgram->create(
            $this->faker->name,
            $this->faker->text,
            ProgramTypeEnum::INDIVIDUAL,
            array_map(static fn(User $user) => $user->getId(), $this->users),
            Uuid::uuid1()->toString(),
            null,
        );
    }

    public function testInvalidUsers(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(User::class) . '/');
        $this->createProgram->create(
            $this->faker->name,
            $this->faker->text,
            ProgramTypeEnum::INDIVIDUAL,
            [Uuid::uuid1()->toString()],
            null,
            null,
        );
    }
}
