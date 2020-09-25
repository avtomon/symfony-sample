<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180808191957
 * @package Application\Migrations
 */
final class Version20180808191957 extends AbstractMigration
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

        $this->addSql('CREATE TABLE billing.invoice (
          id SERIAL8 NOT NULL, 
          source_balance_id BIGINT DEFAULT NULL, 
          target_balance_id BIGINT DEFAULT NULL, 
          amount BIGINT NOT NULL , 
          type VARCHAR(255) NOT NULL, 
          event_order_id BIGINT DEFAULT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_invoice_event_order_id ON billing.invoice (event_order_id)');
        $this->addSql('CREATE INDEX idx_invoice_source_balance_id ON billing.invoice (source_balance_id)');
        $this->addSql('CREATE INDEX idx_invoice_target_balance_id ON billing.invoice (target_balance_id)');
        $this->addSql('ALTER TABLE 
          billing.invoice 
        ADD 
          CONSTRAINT fk_invoice_source_balance_id FOREIGN KEY (source_balance_id)
           REFERENCES billing.balance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          billing.invoice 
        ADD 
          CONSTRAINT fk_invoice_target_balance_id FOREIGN KEY (target_balance_id) 
          REFERENCES billing.balance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          billing.invoice 
        ADD 
          CONSTRAINT fk_invoice_event_order_id FOREIGN KEY (event_order_id) 
          REFERENCES billing.event_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing.transaction ADD invoice_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE billing.transaction DROP type;');
        $this->addSql('ALTER TABLE 
          billing.transaction 
        ADD 
          CONSTRAINT fk_transaction_invoice_id FOREIGN KEY (invoice_id) 
          REFERENCES billing.invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_transaction_invoice_id ON billing.transaction (invoice_id)');
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

        $this->addSql('ALTER TABLE billing.transaction DROP CONSTRAINT fk_transaction_invoice_id');
        $this->addSql('ALTER TABLE billing.transaction ADD type VARCHAR(255) NOT NULL');
        $this->addSql('DROP TABLE billing.invoice');
        $this->addSql('DROP INDEX billing.idx_transaction_invoice_id');
        $this->addSql('ALTER TABLE billing.transaction DROP invoice_id');
    }
}
