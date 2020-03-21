<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\User\CreateUserNotifier;
use App\Domain\Enum\HostEnum;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class CreateUserEmailNotifier implements CreateUserNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(User $user, string $tokenPassword, ?User $coach = null): void
    {
        $this->mailer->send($user->getEmail(), 'Bienvenue !', 'emails/user/create.user.html.twig', [
            'user' => $user,
            'coach' => $coach,
            'update_password_url'=>
                $this->envVarHelper->fetch(HostEnum::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(HostEnum::HOST_URL) . 'update-password?token=' . $tokenPassword,
        ]);
    }
}
