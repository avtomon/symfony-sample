<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20181011232850
 *
 * @package Application\Migrations
 */
final class Version20181011232850 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add updated_at field in balances table';
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

        $this->addSql('ALTER TABLE billing."balance" ADD COLUMN "updated_at" timestamp');
        $this->addSql('UPDATE billing."balance" SET "updated_at" = "created_at"');
        $this->addSql('ALTER TABLE billing."balance" ALTER COLUMN "updated_at" SET DEFAULT now()');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE billing."balance" DROP COLUMN "updated_at"');
    }
}
