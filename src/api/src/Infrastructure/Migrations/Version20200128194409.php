<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\UserTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200128194409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add User Types in database';
    }

    public function up(Schema $schema) : void
    {
        $this->connection->insert('users_types', [
            'id' => UserTypeEnum::ADMINISTRATOR,
            'label' => UserTypeEnum::ADMINISTRATOR_LABEL,
            'description' => 'Type d\'utilisateur administrateur',
        ]);
        $this->connection->insert('users_types', [
            'id' => UserTypeEnum::CANDIDATE,
            'label' => UserTypeEnum::CANDIDATE_LABEL,
            'description' => 'Type d\'utilisateur candidat',
        ]);
        $this->connection->insert('users_types', [
            'id' => UserTypeEnum::COACH,
            'label' => UserTypeEnum::COACH_LABEL,
            'description' => 'Type d\'utilisateur coach',
        ]);
        $this->connection->insert('users_types', [
            'id' => UserTypeEnum::SUPPORT,
            'label' => UserTypeEnum::SUPPORT_LABEL,
            'description' => 'Type d\'utilisateur support',
        ]);
    }

    public function down(Schema $schema) : void
    {
    }
}
