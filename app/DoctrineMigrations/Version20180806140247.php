<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180806140247
 *
 * @package Application\Migrations
 */
final class Version20180806140247 extends AbstractMigration
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

        $this->addSql('CREATE TABLE billing.currency (
          id SERIAL4 NOT NULL, 
          currency_code VARCHAR(255) NOT NULL, 
          multiplier BIGINT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uidx_currency_currency_code ON billing.currency (currency_code)');
        $this->addSql('CREATE TABLE billing.currency_rate (
          id SERIAL4 NOT NULL, 
          source_currency_code VARCHAR(255) NOT NULL, 
          target_currency_code VARCHAR(255) NOT NULL, 
          rate BIGINT NOT NULL, 
          multiplier BIGINT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uidx_currency_rate_currency_code ON billing.currency_rate (
          source_currency_code, target_currency_code
        )');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE billing.currency');
        $this->addSql('DROP TABLE billing.currency_rate');
    }
}
