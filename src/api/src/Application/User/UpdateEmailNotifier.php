<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;

interface UpdateEmailNotifier
{
    public function notify(User $user, string $newEmail, string $tokenEmail): void;
}
