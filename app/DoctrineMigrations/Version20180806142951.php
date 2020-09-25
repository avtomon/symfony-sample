<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180806142951
 * @package Application\Migrations
 */
final class Version20180806142951 extends AbstractMigration
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

        $this->addSql('CREATE TABLE billing.settings (
            id SERIAL4 NOT NULL, 
            key VARCHAR(255) NOT NULL, 
            value JSONB NOT NULL, 
            type VARCHAR(255) NOT NULL, 
            description VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uidx_settings ON billing.settings (key)');
    }


    /**vbyene
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('DROP TABLE billing.settings');
    }
}
