<?php

namespace Extension;

use Codeception\Events;
use Codeception\Extension;

/**
 * Class DatabaseMigrationExtension
 *
 * @package Extension
 */
class DatabaseMigrationExtension extends Extension
{
    public static $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite() : void
    {
        try {
            $_ENV['TEST'] = 'test';

            /** @var \Codeception\Module\Cli $cli */
            $cli = $this->getModule('Cli');
            /** @var \Codeception\Module\Db $db */
            $db = $this->getModule('Db');

            $this->writeln('Clearing the DB...');
            $db->_getDbh()->exec('DROP SCHEMA IF EXISTS z_test_' . getenv('VERSION') . ' CASCADE');
            $db->_getDbh()->exec('DROP SCHEMA IF EXISTS billing CASCADE');
            $db->_getDbh()->exec('DELETE FROM public.migration_versions');
            $db->_getDbh()->exec('CREATE SCHEMA billing');

            $this->writeln('Running Doctrine Migrations...');
            $cli->runShellCommand('bin/console doctrine:migrations:migrate --env=test --no-interaction');
            $cli->seeResultCodeIs(0);

            $this->writeln('Applying stored procedures...');
            $this->writeln('importing procedures...');
            $cli->runShellCommand('bin/console stored-procedure:import --env=test');
            $cli->seeResultCodeIs(0);

            $this->writeln('Load fixtures...');
            $cli->runShellCommand('bin/console doctrine:fixtures:load --no-interaction --env=test');
            $cli->seeResultCodeIs(0);

            $this->writeln('Adding data to table settings...');
            $cli->runShellCommand('bin/console app:update-setting-coefficients --env=test');
            $cli->seeResultCodeIs(0);

            $this->writeln('Test database cleared');
        } catch (\Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred whilst rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
