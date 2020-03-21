<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;

final class UpdateProgram
{
    private ProgramRepository $programRepository;
    private ProgramModelRepository $programModelRepository;
    private UserRepository $userRepository;

    public function __construct(ProgramRepository $programRepository, ProgramModelRepository $programModelRepository, UserRepository $userRepository)
    {
        $this->programRepository = $programRepository;
        $this->programModelRepository = $programModelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws InvalidDateValue
     */
    public function update(string $id, string $name, string $description, string $type, array $userIds, ?string $coachId = null, ?string $modelId = null, ?string $dateStart = null, ?string $dateEnd = null): Program
    {
        $program = $this->programRepository->mustFindOneById($id);
        if (! empty($modelId)) {
            $programModel = $this->programModelRepository->mustFindOneById($modelId);
            $program->setProgramModel($programModel);
        } else {
            $program->setProgramModel(null);
        }

        $program->setName($name);
        $program->setDescription($description);
        $program->setType($type);

        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $program->setUsers($userBeans);

        $currentUser = $this->userRepository->getLoggedUser();
        $program->setCoach($coachId && $currentUser->getType()->getId() === UserTypeEnum::ADMINISTRATOR ? $this->userRepository->mustFindOneById($coachId) : $currentUser);

        if (! empty($dateStart)) {
            $program->setDateStart(new DateTimeImmutable($dateStart));
        }

        if (! empty($dateEnd)) {
            $program->setDateEnd(new DateTimeImmutable($dateEnd));
        }

        $this->programRepository->save($program);

        return $program;
    }
}
