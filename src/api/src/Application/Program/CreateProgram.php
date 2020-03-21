<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Application\Event\CreateEventDraftFromEventModel;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;

final class CreateProgram
{
    private ProgramRepository $programRepository;
    private ProgramModelRepository $programModelRepository;
    private UserRepository $userRepository;
    private CreateEventDraftFromEventModel $createEventDraftFromEventModel;

    public function __construct(ProgramRepository $programRepository, ProgramModelRepository $programModelRepository, UserRepository $userRepository, CreateEventDraftFromEventModel $createEventDraftFromEventModel)
    {
        $this->programRepository = $programRepository;
        $this->programModelRepository = $programModelRepository;
        $this->userRepository = $userRepository;
        $this->createEventDraftFromEventModel = $createEventDraftFromEventModel;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function create(string $name, string $description, string $type, array $userIds, ?string $coachId = null, ?string $modelId = null): Program
    {
        $program = new Program($name, $description, $type);

        $programModel = null;
        if ($modelId !== null) {
            $programModel = $this->programModelRepository->mustFindOneById($modelId);
            $program->setProgramModel($programModel);
        }

        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $program->setUsers($userBeans);

        $currentUser = $this->userRepository->getLoggedUser();
        $program->setCoach($coachId && $currentUser->getType()->getId() === UserTypeEnum::ADMINISTRATOR ? $this->userRepository->mustFindOneById($coachId) : $currentUser);

        $this->programRepository->save($program);

        if ($programModel !== null) {
            foreach ($programModel->getEventModels() as $eventModel) {
                $createdBy = $program->getCreatedBy();
                $this->createEventDraftFromEventModel->create($eventModel->getId(), $userIds, $createdBy !== null ? $createdBy->getId() : '', $program->getId());
            }
        }

        return $program;
    }
}
