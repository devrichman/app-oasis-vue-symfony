<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200130060917 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create reset password token table';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('reset_password_tokens')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('access_token')->string(255)->unique()->notNull()
            ->column('user_id')->references('users')->notNull();
    }

    public function down(Schema $schema) : void
    {
    }
}
