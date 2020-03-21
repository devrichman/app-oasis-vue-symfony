<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Model\User;
use App\Infrastructure\Dao\UserDao;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function get_class;
use function Safe\sprintf;

final class UserProvider implements UserProviderInterface
{
    private UserDao $userDao;

    public function __construct(UserDao $userDao)
    {
        $this->userDao = $userDao;
    }

    /**
     * @param mixed $username
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->userDao->findOneByEmail($username);
        if ($user === null) {
            throw new UsernameNotFoundException(sprintf('User "%s" does not exist.', $username));
        }

        return new SerializableUser($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof SerializableUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param mixed $class
     */
    public function supportsClass($class): bool
    {
        return $class === User::class || $class === SerializableUser::class;
    }
}
