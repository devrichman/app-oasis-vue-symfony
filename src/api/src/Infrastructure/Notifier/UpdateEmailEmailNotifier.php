<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Application\User\UpdateEmailNotifier;
use App\Domain\Enum\HostEnum;
use App\Domain\Model\User;
use App\Infrastructure\Config\EnvVarHelper;

class UpdateEmailEmailNotifier implements UpdateEmailNotifier
{
    private Mailer $mailer;
    private EnvVarHelper $envVarHelper;

    public function __construct(Mailer $mailer, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->envVarHelper = $envVarHelper;
    }

    public function notify(User $user, string $newEmail, string $tokenEmail): void
    {
        $this->mailer->send($newEmail, "Validation de votre changement d'adresse mail", 'emails/user/update.email.html.twig', [
            'update_email_url'=>
                $this->envVarHelper->fetch(HostEnum::HOST_PROTOCOL) . '://' . $this->envVarHelper->fetch(HostEnum::HOST_URL) . 'email-validation/' . $tokenEmail,
        ]);
    }
}
