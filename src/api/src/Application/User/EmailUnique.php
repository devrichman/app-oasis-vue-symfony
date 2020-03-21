<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\Exist;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

final class EmailUnique
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exist
     */
    public function emailUnique(
        string $email,
        ?string $userId = null
    ): bool {
        if (! $this->userRepository->checkEmailUnique($email, $userId)) {
            throw new Exist(User::class, [], true);
        }

        return true;
    }
}
