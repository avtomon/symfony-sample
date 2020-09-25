<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
use AppBundle\Constants\InvoiceTypes;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\_support\Helper\RandomTrait;

class Functional extends \Codeception\Module
{
    use RandomTrait;

    /**
     * @return \AppKernel
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getKernel() : \AppKernel
    {
        return $this->getModule('Symfony')->kernel;
    }

    /**
     * @return ContainerInterface
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getContainer() : ContainerInterface
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * @return Connection
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getDatabaseConnection() : Connection
    {
        static $connection;

        if (null === $connection) {
            $connection = $this->getContainer()->get('database_connection');
        }

        return $connection;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getEntityManager() : \Doctrine\Common\Persistence\ObjectManager
    {
        static $connection;

        if (null === $connection) {
            $connection = $this->getContainer()->get('doctrine')->getManager();
        }

        return $connection;
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function clearState() : void
    {
        $this->getDatabaseConnection()->exec('TRUNCATE TABLE billing."balance" CASCADE');
        $this->getDatabaseConnection()->exec('TRUNCATE TABLE billing."event_order" CASCADE');
        $this->getDatabaseConnection()->exec('TRUNCATE TABLE billing."invoice" CASCADE');
        $this->getDatabaseConnection()->exec('TRUNCATE TABLE billing."transaction" CASCADE');
        $this->getDatabaseConnection()->exec('TRUNCATE TABLE billing."transaction_token" CASCADE');
    }

    /**
     * @return \AppBundle\Doctrine\StoredProcedureManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function storedProcedure() : \AppBundle\Doctrine\StoredProcedureManager
    {
        static $manager;

        if (null === $manager) {
            $manager = $this->getContainer()->get('app.doctrine.stored_procedure');
        }

        return $manager;
    }

    /**
     * @return \AppBundle\Manager\SettingManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getSettingManager() : \AppBundle\Manager\SettingManager
    {
        static $settingManager;

        if (null === $settingManager) {
            $settingManager = $this->getContainer()->get('app.setting_manager');
        }

        return $settingManager;
    }

    /**
     * @return \AppBundle\Manager\OrderManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getOrderManager() : \AppBundle\Manager\OrderManager
    {
        static $orderManager;

        if (null === $orderManager) {
            $orderManager = $this->getContainer()->get('app.order_manager');
        }

        return $orderManager;
    }

    /**
     * @return \AppBundle\Manager\ObjectDataManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getObjectDataManager() : \AppBundle\Manager\ObjectDataManager
    {
        static $objectDataManager;

        if (null === $objectDataManager) {
            $objectDataManager = $this->getContainer()->get('app.object_data_manager');
        }

        return $objectDataManager;
    }

    /**
     * @return \AppBundle\Manager\BalanceManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getBalanceManager() : \AppBundle\Manager\BalanceManager
    {
        static $balanceManager;

        if (null === $balanceManager) {
            $balanceManager = $this->getContainer()->get('app.balance_manager');
        }

        return $balanceManager;
    }

    /**
     * @return \AppBundle\Assembler\PagesDTOAssembler|object
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getPagesAssembler()
    {
        static $pagesAssembler;

        if (null === $pagesAssembler) {
            $pagesAssembler = $this->getContainer()->get('app.assembler.pages_assembler');
        }

        return $pagesAssembler;
    }

    /**
     * @return \AppBundle\Manager\CurrencyRateManager
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getCurrencyRateManager() : \AppBundle\Manager\CurrencyRateManager
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $validator = $this->getContainer()->get('validator');
        return new \AppBundle\Manager\CurrencyRateManager($doctrine, $validator);
    }

    /**
     * @return \Money\MoneyParser
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function getMoneyParser() : \Money\MoneyParser
    {
        static $parser;

        if (null === $parser) {
            $parser = $this->getContainer()->get('app.decimal_money_parser');
        }

        return $parser;
    }

    /**
     * Создает случайный токен и регистрирует его.
     *
     * @param string $type
     * @param string $prefix
     *
     * @return int
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTransactionTokenId($type = 'create', $prefix = '') : int
    {
        $token = \uniqid($prefix, false);
        $db = $this->getDatabaseConnection();
        $sql = "INSERT INTO billing.transaction_token (token, type) VALUES ('$token', '$type') RETURNING id;";
        $rows = $db->query($sql)->fetchAll();

        return (int)$rows[0]['id'];
    }

    /**
     * Accrue start balance to object
     *
     * @param string $objectType
     * @param int $objectId
     * @param string $accountType
     * @param string $balanceType
     * @param \Money\Money $money
     * @param string $invoiceType
     *
     * @return int
     *
     * @throws \Codeception\Exception\ModuleException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PgFunc\Exception
     * @throws \PgFunc\Exception\Usage
     */
    public function accrueBalance(
        string $objectType,
        int $objectId,
        string $accountType,
        string $balanceType,
        \Money\Money $money,
        string $invoiceType = InvoiceTypes::PAYMENT
    ) : int
    {
        $transactionTokenId = $this->getTransactionTokenId('create', 'accrue_balance_');

        $invoiceId = $this->storedProcedure()
            ->invoiceCreate(
                $invoiceType,
                $money,
                $accountType,
                $balanceType,
                $objectType,
                $objectId,
                $transactionTokenId
            );
        $this->assertGreaterThan(0, $invoiceId, 'Invalid invoice id');

        return $this->storedProcedure()
            ->invoiceProcessing($invoiceId);
    }

    /**
     * @param string $objectType
     * @param int $objectId
     * @param string $balanceType
     * @param string $currencyCode
     * @param string $accountType
     *
     * @return array
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function grabBalance(
        string $objectType,
        int $objectId,
        string $balanceType,
        string $currencyCode,
        string $accountType
    ) : array
    {
        $connection = $this->getDatabaseConnection();
        $qb = $connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from('balance')
            ->where('object_type = ? AND object_id = ? AND type = ? AND currency_code = ? AND account_type = ?')
            ->setParameters([$objectType, $objectId, $balanceType, $currencyCode, $accountType]);

        $stmt = $qb->execute();
        $actual = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->debugSection('Query', $qb->getSQL());
        $this->debugSection('Parameters', $qb->getParameters());

        $this->assertNotEmpty($actual, 'Balance dsn`t exists.');

        return $actual;
    }

    /**
     * @param int $sourceBalanceId
     * @param int $targetBalanceId
     * @param string $invoiceType
     *
     * @return array
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function grabLastInvoice(
        ?int $sourceBalanceId,
        ?int $targetBalanceId,
        string $invoiceType
    ) : array
    {
        $connection = $this->getDatabaseConnection();
        $qb = $connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from('invoice')
            ->where('type = :type')
            ->setParameter(':type', $invoiceType)
            ->orderBy('created_at', 'desc')
            ->setMaxResults(1);

        if (null === $sourceBalanceId) {
            $qb->andWhere('source_balance_id IS NULL');
        } else {
            $qb->andWhere('source_balance_id = :sb')->setParameter(':sb', $sourceBalanceId);
        }
        if (null === $targetBalanceId) {
            $qb->andWhere('target_balance_id IS NULL');
        } else {
            $qb->andWhere('target_balance_id = :tb')->setParameter(':tb', $targetBalanceId);
        }

        $stmt = $qb->execute();
        $actual = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->debugSection('Query', $qb->getSQL());
        $this->debugSection('Parameters', $qb->getParameters());

        $this->assertNotEmpty($actual, 'Invoice dsn`t exists.');

        return $actual;
    }

    /**
     * @param int $transactionTokenId
     * @param array|null|string $field
     *
     * @return array
     *
     * @throws \Codeception\Exception\ModuleException
     */
    public function grabInvoicesByTransactionTokenId(int $transactionTokenId, array $field = null) : array
    {
        $field = $field ? implode(', ', $field) : '*';

        $connection = $this->getDatabaseConnection();
        $qb = $connection->createQueryBuilder();
        $qb
            ->select($field)
            ->from('invoice')
            ->where('transaction_token_id = :transaction_token_id')
            ->setParameter(':transaction_token_id', $transactionTokenId)
            ->orderBy('id', 'asc');

        $stmt = $qb->execute();
        $actual = $stmt->fetchAll();

        $this->debugSection('Query', $qb->getSQL());
        $this->debugSection('Parameters', $qb->getParameters());

        $this->assertNotEmpty($actual, 'Invoice dsn`t exists.');

        return $actual;
    }

    /**
     * @param $value
     * @param null $label
     */
    public function showValue($value, $label = null) : void
    {
        if ($label) {
            $this->debugSection($label, json_encode($value));
        } else {
            $this->debug(json_encode($value));
        }
    }
}
