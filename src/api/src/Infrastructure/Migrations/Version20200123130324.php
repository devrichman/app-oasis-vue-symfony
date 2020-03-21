<?php

declare(strict_types=1);

namespace App\Infrastructure\Migrations;

use App\Domain\Enum\RightEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\Role;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123130324 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add administrateur role and rights.';
    }

    public function up(Schema $schema) : void
    {
        $this->connection
            ->insert(
                'roles',
                [
                    'id' => RoleEnum::ADMINISTRATEUR_ID,
                    'name' => RoleEnum::ADMINISTRATEUR_NAME,
                    'description' => 'Rôle ayant tous les droits',
                    'displayable' => '0',
                ]
            );

        $rights = [
            RightEnum::CREATE_ROLE_CODE => 'Permet de créer un rôle',
            RightEnum::UPDATE_ROLE_CODE => 'Permet de modifier un rôle',
            RightEnum::DELETE_ROLE_CODE => 'Permet de supprimer un rôle',

            RightEnum::ACCESS_USER_CODE => 'Permet d’accéder au menu « Utilisateurs »',
            RightEnum::CREATE_USER_CODE => 'Permet de créer un utilisateur',
            RightEnum::UPDATE_USER_CODE => 'Permet de modifier un utilisateur',
            RightEnum::DELETE_USER_CODE => 'Permet d\'archiver un utilisateur',
            RightEnum::DISABLE_USER_CODE => 'Permet de désactiver un utilisateur',

            RightEnum::ACCESS_COMPANY_CODE => 'Permet d\'accéder au menu « Entreprises »',
            RightEnum::CREATE_COMPANY_CODE => 'Permet de créer une entreprise',
            RightEnum::UPDATE_COMPANY_CODE => 'Permet de modifier les informations d’une entreprise',
            RightEnum::DELETE_COMPANY_CODE => 'Permet de supprimer une entreprise',

            RightEnum::ACCESS_EVENT_CODE => 'Permet d\'accéder à la liste de tous les événements',
            RightEnum::CREATE_EVENT_CODE => 'Permet de créer un événement spécifique',
            RightEnum::UPDATE_EVENT_CODE => 'Permet de modifier un événement spécifique',
            RightEnum::DELETE_EVENT_CODE => 'Permet de supprimer un événement spécifique',

            RightEnum::ACCESS_PROGRAM_CODE => 'Permet d\'accéder à la liste de toutes les prestations spécifiques',
            RightEnum::CREATE_PROGRAM_CODE => 'Permet de créer une prestation spécifique',
            RightEnum::UPDATE_PROGRAM_CODE => 'Permet de modifier une prestation spécifique',
            RightEnum::DELETE_PROGRAM_CODE => 'Permet de supprimer une prestation spécifique',

            RightEnum::CREATE_DOCUMENT_CODE => 'Permet de créer un document',
            RightEnum::UPDATE_DOCUMENT_CODE => 'Permet de modifier un document',
            RightEnum::DELETE_DOCUMENT_CODE => 'Permet de supprimer un document',
        ];

        $order = 0;
        foreach ($rights as $rightCode => $name) {
            $rightId = Uuid::uuid4()->toString();
            $order++;

            $this->connection
                ->insert(
                    'rights',
                    [
                        'id' => $rightId,
                        'name' => $name,
                        'order_view' => $order,
                        'code' => $rightCode,
                    ]
                );

            $this->connection
                ->insert(
                    'roles_rights',
                    [
                        'role_id' => RoleEnum::ADMINISTRATEUR_ID,
                        'right_id' => $rightId
                    ]
                );
        }


    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
