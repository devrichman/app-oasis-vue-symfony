<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\EmailUnique;
use App\Domain\Exception\Exist;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

class EmailUniqueController
{
    private EmailUnique $emailUnique;

    public function __construct(EmailUnique $updateUser)
    {
        $this->emailUnique = $updateUser;
    }

    /**
     * @throws Exist
     *
     * @Query()
     * @Logged()
     */
    public function emailUnique(
        string $email,
        ?string $userId = null
    ): bool {
        return $this->emailUnique->emailUnique($email, $userId);
    }
}
