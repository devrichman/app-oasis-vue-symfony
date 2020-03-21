<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\AddEventToProgram;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class AddEventToProgramController extends AbstractController
{
    private AddEventToProgram $addEventToProgram;

    public function __construct(AddEventToProgram $addEventToProgram)
    {
        $this->addEventToProgram = $addEventToProgram;
    }

    /**
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function addEventToProgram(string $programId, string $eventId): void
    {
        //$this->addEventToProgram->add($programId, $eventId);
    }
}
