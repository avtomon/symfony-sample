<?php

declare(strict_types=1);

namespace Application\Migrations;

use AppBundle\Constants\TransactionTokenTypes;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180828124721
 * @package Application\Migrations
 */
final class Version20180828124721 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Creating transaction token table';
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

        $this->addSql(sprintf('CREATE TYPE billing.transaction_token_types_enum AS ENUM(\'%s\')',
            implode('\',\'', [
                TransactionTokenTypes::CREATE,
                TransactionTokenTypes::COMPLETE,
                TransactionTokenTypes::CANCEL,
            ])
        ));
        $this->addSql('CREATE TABLE billing.transaction_token (
          id SERIAL8 NOT NULL, 
          token TEXT NOT NULL, 
          type billing.transaction_token_types_enum, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT now() NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('ALTER TABLE billing.invoice ADD transaction_token_id BIGINT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE billing.invoice ADD 
             CONSTRAINT fk_invoice_transaction_token_id 
             FOREIGN KEY (transaction_token_id) 
             REFERENCES billing.transaction_token (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('CREATE INDEX idx_invoice_transaction_token_id ON billing.invoice (transaction_token_id)');
        $this->addSql('CREATE UNIQUE INDEX uidx_transaction_token_type_token ON billing.transaction_token (type, token)');
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

        $this->addSql('DROP INDEX billing.uidx_transaction_token_type_token');
        $this->addSql('ALTER TABLE billing.invoice DROP CONSTRAINT fk_invoice_transaction_token_id');
        $this->addSql('DROP TABLE billing.transaction_token');
        $this->addSql('DROP INDEX billing.idx_invoice_transaction_token_id');
        $this->addSql('ALTER TABLE billing.invoice DROP transaction_token_id');
        $this->addSql('DROP TYPE billing.transaction_token_types_enum');
    }
}
