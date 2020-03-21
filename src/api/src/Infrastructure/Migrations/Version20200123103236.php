<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123103236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create the session table';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('sessions')
            ->column('sess_id')->string(128)->notNull()->primaryKey()
            ->column('sess_data')->blob()->notNull()
            ->column('sess_time')->integer()->notNull()
            ->column('sess_lifetime')->integer()->notNull();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
