<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20180824074750
 * @package Application\Migrations
 */
final class Version20180824074750 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Set default value now() for every created_at field';
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

        $this->addSql('ALTER TABLE billing.balance ALTER COLUMN created_at SET DEFAULT now();');
        $this->addSql('ALTER TABLE billing.invoice ALTER COLUMN created_at SET DEFAULT now();');
        $this->addSql('ALTER TABLE billing.transaction ALTER COLUMN created_at SET DEFAULT now();');
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

        $this->addSql('ALTER TABLE billing.balance ALTER COLUMN created_at SET DEFAULT NULL');
        $this->addSql('ALTER TABLE billing.invoice ALTER COLUMN created_at SET DEFAULT NULL');
        $this->addSql('ALTER TABLE billing.transaction ALTER COLUMN created_at SET DEFAULT NULL');

    }
}
