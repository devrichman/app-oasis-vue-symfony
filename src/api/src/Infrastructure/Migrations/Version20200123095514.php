<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\CivilityEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123095514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create users, roles and rights tables';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('users_types')
            ->column('id')->string(50)->primaryKey()->graphqlField()
            ->column('label')->string(50)->notNull()->graphqlField()
            ->column('description')->text()->null()->default(null)->graphqlField();

        $db->table('companies')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('code')->string(255)->notNull()->unique()->graphqlField()
            ->column('salesforce_link')->string(255)->null()->default(null)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField();

        $db->table('file_descriptors')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('size')->integer()->notNull()->graphqlField()
            ->column('upstream')->string(255)->notNull()->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField();

        $db->table('users')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('first_name')->string(255)->notNull()->graphqlField()
            ->column('last_name')->string(255)->notNull()->graphqlField()
            ->column('email')->string(255)->notNull()->unique()->graphqlField()
            ->column('phone')->string(255)->notNull()->graphqlField()
            ->column('coach_id')->references('users')->null()->default(null)->graphqlField()
            ->column('type_id')->references('users_types')->notNull()->graphqlField()
            ->column('company_id')->references('companies')->null()->default(null)->graphqlField()
            ->column('profile_picture_id')->references('file_descriptors')->null()->default(null)->graphqlField()
            ->column('password')->string(255)->null()->default(null)->graphqlField()
            ->column('status')->boolean()->notNull()->default(true)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField()
            ->column('cgu_accepted')->boolean()->notNull()->default(false)->graphqlField()
            ->column('civility')->string(10)->notNull()->default(CivilityEnum::MISTER_CODE)->graphqlField()
            ->column('linkedin')->string(255)->null()->default(null)->graphqlField()
            ->column('address')->text()->null()->default(null)->graphqlField()
            ->column('function')->string(255)->null()->default(null)->graphqlField()
            ->column('seniority_date')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('previous_function')->text()->null()->default(null)->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField();


        $db->table('roles')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->unique()->graphqlField()
            ->column('description')->text()->null()->default(null)->graphqlField()
            ->column('displayable')->boolean()->notNull()->default(true)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();

        $db->table('rights')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->unique()->graphqlField()
            ->column('order_view')->integer()->notNull()->unique()->graphqlField()
            ->column('code')->string(255)->notNull()->unique()->graphqlField();

        $db->table('companies')
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField();

        $db->table('file_descriptors')
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField();

        $db->table('documents')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->graphqlField()
            ->column('description')->text()->notNull()->graphqlField()
            ->column('tags')->string(255)->notNull()->graphqlField()
            ->column('visibility')->string(255)->notNull()->graphqlField()
            ->column('file_descriptor_id')->references('file_descriptors')->notNull()->graphqlField()
            ->column('author')->references('users')->notNull()->graphqlField()
            ->column('elaboration_date')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('created_by')->references('users')->null()->default(null)->graphqlField()
            ->column('updated_at')->datetimeImmutable()->null()->default(null)->graphqlField()
            ->column('updated_by')->references('users')->null()->default(null)->graphqlField()
            ->column('deleted')->boolean()->notNull()->default(false)->graphqlField();

        $db->table('update_email_tokens')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('access_token')->string(255)->unique()->notNull()
            ->column('new_email')->string(255)->notNull()->unique()->graphqlField()
            ->column('user_id')->references('users')->notNull()->graphqlField();

        $db->junctionTable('users', 'roles')->graphqlField();

        $db->junctionTable('roles', 'rights')->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
