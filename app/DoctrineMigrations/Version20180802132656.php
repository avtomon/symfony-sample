<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Добавление транзакций, балансов и истории ордеров
 */
final class Version20180802132656 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE billing.balance (
          id SERIAL8 NOT NULL, 
          object_type VARCHAR(255) NOT NULL, 
          object_id BIGINT NOT NULL, 
          currency_code VARCHAR(255) NOT NULL, 
          amount BIGINT NOT NULL, 
          type VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uidx_relations_object ON billing.balance (
          object_type, object_id, type, currency_code
        )');
        $this->addSql('CREATE TABLE billing.transaction (
          id SERIAL8 NOT NULL, 
          balance_id BIGINT DEFAULT NULL, 
          amount BIGINT NOT NULL, 
          type VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_transaction_balance_id ON billing.transaction (balance_id)');
        $this->addSql('CREATE TABLE billing.event_order (
          id SERIAL8 NOT NULL, 
          order_id BIGINT NOT NULL, 
          object_type VARCHAR(255) NOT NULL, 
          object_id BIGINT NOT NULL, 
          currency_code VARCHAR(255) NOT NULL, 
          status VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_event_order_id ON billing.event_order (order_id)');
        $this->addSql(
            'ALTER TABLE billing.transaction 
             ADD CONSTRAINT fk_transaction_balance_id FOREIGN KEY (balance_id) 
             REFERENCES billing.balance (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE billing.balance');
        $this->addSql('DROP TABLE billing.transaction');
        $this->addSql('DROP TABLE billing.event_order');
    }
}
