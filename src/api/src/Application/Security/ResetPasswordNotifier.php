<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Model\User;

interface ResetPasswordNotifier
{
    public function notify(User $user, string $tokenPassword): void;
}
