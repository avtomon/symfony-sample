<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181215152924 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add next and prev invoice ids';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE billing.invoice ADD COLUMN prev_id BIGINT');
        $this->addSql('ALTER TABLE billing.invoice ADD COLUMN next_id BIGINT');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE billing.invoice DROP COLUMN prev_id');
        $this->addSql('ALTER TABLE billing.invoice DROP COLUMN next_id');
    }
}
