<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\Security\ResetPasswordNotifier;
use App\Domain\Enum\HostEnum;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class ResetPasswordEmailNotifier implements ResetPasswordNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(User $user, string $tokenPassword): void
    {
        $this->mailer->send($user->getEmail(), 'Réinitialisation de votre mot de passe', 'emails/security/reset.password.html.twig', [
            'update_password_url'=>
        $this->envVarHelper->fetch(HostEnum::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(HostEnum::HOST_URL) . 'update-password?token=' . $tokenPassword,
        ]);
    }
}
