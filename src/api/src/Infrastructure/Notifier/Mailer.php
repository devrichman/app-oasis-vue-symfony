<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Enum\TemplateEmailEnum;
use App\Infrastructure\Config\EnvVarHelper;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

final class Mailer
{
    private const MAILER_FROM_ENV_VAR = 'MAILER_FROM';

    private Swift_Mailer $mailer;
    private Environment $twig;
    private EnvVarHelper $envVarHelper;
    private string $mailerFrom;

    public function __construct(Swift_Mailer $mailer, Environment $twig, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->envVarHelper = $envVarHelper;
        $this->mailerFrom = $envVarHelper->fetch(self::MAILER_FROM_ENV_VAR);
    }

    /**
     * @param mixed[] $context
     */
    public function send(string $to, string $subject, string $template, array $context = []): int
    {
        $message = (new Swift_Message($subject))
            ->setFrom($this->mailerFrom)
            ->setTo($to);

        $context['logo'] = $message->embed(Swift_Image::fromPath('img/logo.svg'));
        $context['background_image_purple'] = $message->embed(Swift_Image::fromPath('img/bg-full.png'));
        $context['background_image_gray'] = $message->embed(Swift_Image::fromPath('img/fond-gris.svg'));

        $context['linkedin'] = $message->embed(Swift_Image::fromPath('img/icon-linkedin.svg'));
        $context['twitter'] = $message->embed(Swift_Image::fromPath('img/twitter.svg'));
        $context['website'] = $message->embed(Swift_Image::fromPath('img/icon-site.svg'));

        $context['site_oasis_link'] = $this->envVarHelper->fetch(TemplateEmailEnum::SITE_OASYS_LINK);
        $context['linkedin_link'] = $this->envVarHelper->fetch(TemplateEmailEnum::LINKEDIN_LINK);
        $context['twitter_link'] = $this->envVarHelper->fetch(TemplateEmailEnum::TWITTER_LINK);

        $message->setBody(
            $this->twig->render(
                $template,
                $context,
            ),
            'text/html'
        );

        return $this->mailer->send($message);
    }
}
