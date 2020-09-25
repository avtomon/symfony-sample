<?php

declare(strict_types=1);

namespace Application\Migrations;

use AppBundle\Constants\TransactionTokenTypes;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20181119121314 extends AbstractMigration
{
    /**
     * @return bool
     */
    public function isTransactional() : bool
    {
        return false;
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
        $this->addSql(sprintf(
            'ALTER TYPE billing.transaction_token_types_enum ADD VALUE IF NOT EXISTS \'%s\'',
            TransactionTokenTypes::PAID
        ));
    }

    public function down(Schema $schema) : void
    {
    }
}
