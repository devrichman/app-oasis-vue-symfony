<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;

interface CreateUserNotifier
{
    public function notify(User $user, string $tokenPassword, ?User $coach = null): void;
}
