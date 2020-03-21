<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Logging\LoggableModel;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Security\SerializableUser;
use Safe\DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use function assert;
use function is_object;
use function Safe\sprintf;

trait ModelLogger
{
    /**
     * Important: must save the model after calling
     * this function!
     */
    private function log(LoggableModel $model): void
    {
        $user = $this->_getUser();

        // case: creation.
        if ($model->getCreatedBy() === null) {
            $model->setCreatedAt(new DateTimeImmutable());
            $model->setCreatedBy($user);

            return;
        }

        // case: update.
        $model->setUpdatedAt(new DateTimeImmutable());
        $model->setUpdatedBy($user);
    }

    abstract protected function getTokenStorage(): TokenStorageInterface;

    abstract protected function getUserRepository(): UserRepository;

    /**
     * Inspired from ControllerTrait.
     */
    private function _getUser(): User
    {
        $token = $this->getTokenStorage()->getToken();
        if ($token === null) {
            throw new AuthenticationCredentialsNotFoundException('token not found');
        }

        $serializableUser = $token->getUser();
        if (! is_object($serializableUser)) {
            // e.g. anonymous authentication
            throw new AuthenticationCredentialsNotFoundException('token not found');
        }
        assert($serializableUser instanceof SerializableUser);

        $user = $this->getUserRepository()->findOneByEmail($serializableUser->getUsername());
        if ($user === null) {
            throw new AuthenticationCredentialsNotFoundException(sprintf('no user with e-mail %s found', $serializableUser->getUsername()));
        }

        return $user;
    }
}
