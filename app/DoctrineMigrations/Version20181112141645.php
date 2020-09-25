<?php

declare(strict_types=1);

namespace Application\Migrations;

use AppBundle\Constants\TransactionTokenTypes;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181112141645
 */
final class Version20181112141645 extends AbstractMigration
{
    /**
     * Indicates the transactional mode of this migration.
     *
     * If this function returns true (default) the migration will be executed
     * in one transaction, otherwise non-transactional state will be used to
     * execute each of the migration SQLs.
     */
    public function isTransactional() : bool
    {
        return false;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql(sprintf(
            'ALTER TYPE billing.transaction_token_types_enum ADD VALUE IF NOT EXISTS \'%s\'',
            TransactionTokenTypes::PAID
        ));
        $this->addSql('ALTER TABLE billing.event_order ADD transaction_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE billing.event_order ADD token_type billing.transaction_token_types_enum DEFAULT NULL');
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

        $this->addSql('ALTER TABLE billing.event_order DROP transaction_token');
        $this->addSql('ALTER TABLE billing.event_order DROP token_type');
    }
}
