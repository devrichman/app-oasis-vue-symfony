<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\EventStatusEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200127205901 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create programs and events tables';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('program_models')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('description')->text()->notNull()->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();

        $db->table('programs')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('description')->text()->notNull()->graphqlField()
            ->column('status')->string(255)->notNull()->default('created')->graphqlField()
            ->column('type')->string(255)->notNull()->graphqlField()
            ->column('date_start')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('date_end')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('program_model_id')->references('program_models')->null()->default(null)->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField()
            ->column('coach_id')->references('users')->null()->default(null)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();

        $db->table('event_models')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('description')->text()->notNull()->graphqlField()
            ->column('type')->string(255)->notNull()->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();

        $db->table('events')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('description')->text()->notNull()->graphqlField()
            ->column('type')->string(255)->notNull()->graphqlField()
            ->column('date_event')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('organizer')->references('users')->null()->default(null)->graphqlField()
            ->column('status')->string(255)->null()->default(EventStatusEnum::CREATED)->graphqlField()
            ->column('event_model_id')->references('event_models')->null()->default(null)->graphqlField()
            ->column('program_id')->references('programs')->null()->default(null)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();
        
        $db->junctionTable('documents', 'events')->graphqlField();
        $db->junctionTable('programs', 'users')->graphqlField();
        $db->junctionTable('events', 'users')->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
