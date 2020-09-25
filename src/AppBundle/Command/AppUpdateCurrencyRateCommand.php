<?php

namespace AppBundle\Command;

use AppBundle\Exception\Http\ValidationHttpException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AppUpdateCurrencyRateCommand
 * @package AppBundle\Command
 */
class AppUpdateCurrencyRateCommand extends ContainerAwareCommand
{
    protected function configure() : void
    {
        $this
            ->setName('app:update-currency-rate')
            ->setDescription('fetch rate of currency from microservice currency');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currencyRateManager = $this->getContainer()->get('app.currency_rate_manager');
        try {
            $currencyRateManager->importFromCurrencyApi();
        } catch (ValidationHttpException $e) {
            $output->writeln($e->getMessage());
            $errors = $e->getErrors();
            foreach ($errors as $error) {
                $output->writeln('Field ' . $error['field'] . ': ' . $error['message']);
            }
        }
        $output->writeln('Currency Api Client: rates updated successful');
    }
}
