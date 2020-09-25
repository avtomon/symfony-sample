<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180801131929
 * @package Application\Migrations
 */
final class Version20180801131929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create migration_procedures table';
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

        $this->addSql(<<<SQL
CREATE TABLE IF NOT EXISTS billing.migration_procedures
(
  proc_name varchar(255) PRIMARY KEY,
  proc_hash varchar(255) NOT NULL,
  created_at timestamptz NOT NULL,
  updated_at timestamptz NOT NULL
);
SQL
        );
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

        $this->addSql('DROP TABLE IF EXISTS billing.migration_procedures');
    }
}
