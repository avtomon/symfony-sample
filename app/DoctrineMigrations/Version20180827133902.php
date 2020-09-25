<?php

declare(strict_types=1);

namespace Application\Migrations;

use AppBundle\Constants\BalanceAccountTypes;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180827133902
 * @package Application\Migrations
 */
final class Version20180827133902 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql(sprintf('CREATE TYPE billing.balance_account_types_enum AS ENUM(\'%s\')',
                implode('\',\'',
                    [BalanceAccountTypes::PERSONAL,])
        ));

        $this->addSql('ALTER TABLE billing.balance ADD account_type billing.balance_account_types_enum');
        $this->addSql('DROP INDEX billing.uidx_relations_object');
        $this->addSql('CREATE UNIQUE INDEX uidx_relations_object '.
            'ON billing.balance (object_type, object_id, type, currency_code, account_type)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('DROP INDEX billing.uidx_relations_object');
        $this->addSql('CREATE UNIQUE INDEX uidx_relations_object '.
            'ON billing.balance (object_type, object_id, type, currency_code)');
        $this->addSql('ALTER TABLE billing.balance DROP account_type');
        $this->addSql('DROP TYPE billing.balance_account_types_enum');
    }
}
