<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180913084548 extends AbstractMigration
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

        $this->addSql('DROP TABLE IF EXISTS billing.migration_procedures');
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

        $this->addSql('CREATE TABLE billing.migration_procedures (
          proc_name VARCHAR(255) NOT NULL,
          proc_hash VARCHAR(255) NOT NULL,
          created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL,
          updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL,
          PRIMARY KEY(proc_name)
        )');
    }
}
