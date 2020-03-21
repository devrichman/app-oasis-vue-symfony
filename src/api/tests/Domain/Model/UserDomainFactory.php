<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Model\User;
use App\Domain\Model\UserType;
use Faker\Generator;

final class UserDomainFactory
{
    public static function createUser(Generator $faker): User
    {
        return new User(
            new UserType($faker->uuid, $faker->name),
            $faker->firstName,
            $faker->lastName,
            $faker->email,
            $faker->phoneNumber
        );
    }
}
