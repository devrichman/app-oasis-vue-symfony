<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324123551 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add program_model_id to event_models';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('event_models')
            ->column('program_model_id')->references('program_models')->null()->default(null)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
    }
}
