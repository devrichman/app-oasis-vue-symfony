<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\UpdateEmailToken;
use App\Domain\Model\User;
use App\Domain\Repository\EmailTokenRepository;
use App\Domain\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UpdateEmail
{
    private UserRepository $userRepository;
    private EmailTokenRepository $emailTokenRepository;
    private UpdateEmailNotifier $updateEmailNotifier;
    private TokenStorageInterface $tokenStorage;
    private SessionInterface $session;

    public function __construct(
        UserRepository $userRepository,
        EmailTokenRepository $emailTokenRepository,
        UpdateEmailNotifier $updateEmailNotifier,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $this->userRepository = $userRepository;
        $this->updateEmailNotifier = $updateEmailNotifier;
        $this->emailTokenRepository = $emailTokenRepository;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @throws NotFound
     * @throws Exist
     * @throws InvalidStringValue
     */
    public function updateEmail(string $id, string $email): User
    {
        $user = $this->userRepository->mustFindOneById($id);

        $exist = $this->userRepository->findOneByEmail($email);

        if ($exist) {
            throw new Exist(User::class, ['email' => 'email'], true);
        }

        $token = Uuid::uuid1()->toString();

        $jwt = $this->emailTokenRepository->encodeToken($user, $token);

        $emailToken = $this->emailTokenRepository->findOneByUserId($user->getId());

        if ($emailToken) {
            $emailToken->setAccessToken($token);
            $emailToken->setNewEmail($email);
        } else {
            $emailToken = new UpdateEmailToken($user, $token, $email);
        }

        $this->emailTokenRepository->save($emailToken);
        $this->updateEmailNotifier->notify($user, $email, $jwt);

        return $user;
    }
}
