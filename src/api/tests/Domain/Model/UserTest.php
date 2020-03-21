<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Exception\InvalidStringValue;
use App\Tests\Domain\DomainTestCase;

final class UserTest extends DomainTestCase
{
    public function testSetFirstName(): void
    {
        $user = UserDomainFactory::createUser($this->faker);

        // correct.
        $firstName = $this->faker->firstName;
        $user->setFirstName($firstName);
        $this->assertEquals($firstName, $user->getFirstName());

        // empty.
        $this->expectException(InvalidStringValue::class);
        $user->setFirstName('');

        // too long.
        $this->expectException(InvalidStringValue::class);
        $user->setFirstName($this->faker->text(256));
    }

    public function testSetLastName(): void
    {
        $user = UserDomainFactory::createUser($this->faker);

        // correct.
        $lastName = $this->faker->lastName;
        $user->setLastName($lastName);
        $this->assertEquals($lastName, $user->getLastName());

        // empty.
        $this->expectException(InvalidStringValue::class);
        $user->setLastName('');

        // too long.
        $this->expectException(InvalidStringValue::class);
        $user->setLastName($this->faker->text(256));
    }

    public function testSetPassword(): void
    {
        $user = UserDomainFactory::createUser($this->faker);

        // correct.
        $password = 'Secret83';
        $user->setPassword($password);
        $this->assertNotNull($user->getPassword());

        // empty.
        $this->expectException(InvalidStringValue::class);
        $user->setPassword('');

        // wrong.
        $this->expectException(InvalidStringValue::class);
        $user->setPassword('secret');
    }

    public function testSetPhone(): void
    {
        $user = UserDomainFactory::createUser($this->faker);

        $validPhoneNumbers = [
            '+33123123123',
            '+33 123 123 123',
            '0 123 123 123',
            '01 234 567 89',
            '123456789',
        ];
        $invalidPhoneNumbers = [
            '',
            '213 4611 2301',
            '221 123 123',
            '02 123 123 12',
            '+33 123 456 7890',
        ];

        foreach ($validPhoneNumbers as $validPhoneNumber) {
            $user->setPhone($validPhoneNumber);
            $this->assertEquals($user->getPhone(), $validPhoneNumber);
        }

        foreach ($invalidPhoneNumbers as $invalidPhoneNumber) {
            $this->expectException(InvalidStringValue::class);
            $user->setPhone($invalidPhoneNumber);
        }
    }

    public function testSetUserInfo(): void
    {
        $user = UserDomainFactory::createUser($this->faker);

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $phone = $this->faker->phoneNumber;
        $civility = 'mme';
        $address = $this->faker->address;
        $linkedin = $this->faker->url;
        $function = $this->faker->jobTitle;
        $seniorityDate = $this->faker->date('Y-m-d');
        $previousFunction = $this->faker->jobTitle;

        $user->setUserInformation($user, $firstName, $lastName, $phone, $civility, $address, $linkedin, $function, $seniorityDate, $previousFunction);

        $this->assertEquals($user->getFirstName(), $firstName);
        $this->assertEquals($user->getLastName(), $lastName);
        $this->assertEquals($user->getPhone(), $phone);
        $this->assertEquals($user->getCivility(), $civility);
        $this->assertEquals($user->getAddress(), $address);
        $this->assertEquals($user->getLinkedin(), $linkedin);
        $this->assertEquals($user->getFunction(), $function);
        $this->assertEquals($user->getSeniorityDate()->format('Y-m-d'), $seniorityDate);
        $this->assertEquals($user->getPreviousFunction(), $previousFunction);
    }
}
