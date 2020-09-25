<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180815143737
 * @package Application\Migrations
 */
final class Version20180815143737 extends AbstractMigration
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

        $this->addSql('ALTER TABLE billing.event_order ALTER amount TYPE BIGINT');
        $this->addSql('ALTER TABLE billing.event_order ALTER amount DROP DEFAULT');
        $this->addSql('ALTER TABLE billing.invoice ADD target_amount BIGINT NOT NULL');
        $this->addSql('ALTER TABLE billing.invoice ADD source_amount BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE billing.invoice ALTER amount DROP NOT NULL');
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

        $this->addSql('ALTER TABLE billing.invoice DROP target_amount');
        $this->addSql('ALTER TABLE billing.invoice DROP source_amount');
        $this->addSql('ALTER TABLE billing.invoice ALTER amount SET NOT NULL');
        $this->addSql('ALTER TABLE billing.event_order ALTER amount TYPE INT');
        $this->addSql('ALTER TABLE billing.event_order ALTER amount DROP DEFAULT');
    }
}
