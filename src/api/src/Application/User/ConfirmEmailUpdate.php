<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\EmailTokenRepository;
use App\Domain\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ConfirmEmailUpdate
{
    private UserRepository $userRepository;
    private EmailTokenRepository $emailTokenRepository;
    private TokenStorageInterface $tokenStorage;
    private SessionInterface $session;

    public function __construct(
        UserRepository $userRepository,
        EmailTokenRepository $emailTokenRepository,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $this->userRepository = $userRepository;
        $this->emailTokenRepository = $emailTokenRepository;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @throws NotFound
     * @throws InvalidValue
     * @throws InvalidStringValue
     */
    public function confirmEmail(string $token): User
    {
        $emailToken = $this->emailTokenRepository->mustCheckValidToken($token);

        $user = $this->userRepository->mustFindOneById($emailToken->getUser()->getId());

        $user->setEmail($emailToken->getNewEmail());

        $this->userRepository->saveNoLog($user);
        $this->emailTokenRepository->delete($emailToken);

        return $user;
    }
}
