<?php

namespace App\Command;

use App\Service\PaymentProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

#[AsCommand(name: 'app:process-payment')]
class ProcessPaymentCommand extends Command
{
    public function __construct(
        private PaymentProcessor $paymentProcessor,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Process a payment via CLI')
            ->addArgument('gateway', InputArgument::REQUIRED, 'The payment gateway to use')
            ->addArgument('amount', InputArgument::OPTIONAL, 'The amount to be processed', 100) // Default: 100
            ->addArgument('currency', InputArgument::OPTIONAL, 'The currency', 'USD') // Default: USD
            ->addOption('card-number', null, InputOption::VALUE_OPTIONAL, 'Card Number')
            ->addOption('card-exp-year', null, InputOption::VALUE_OPTIONAL, 'Card Expiry Year')
            ->addOption('card-exp-month', null, InputOption::VALUE_OPTIONAL, 'Card Expiry Month')
            ->addOption('card-cvv', null, InputOption::VALUE_OPTIONAL, 'Card CVV');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $gateway = $input->getArgument('gateway');
        $amount = (float) $input->getArgument('amount');
        $currency = strtoupper($input->getArgument('currency'));

        // Collect card details if provided, otherwise default
        $cardDetails = array_filter([
            'number' => $input->getOption('card-number') ?? '4242424242424242',
            'expYear' => $input->getOption('card-exp-year') ?? '2025',
            'expMonth' => $input->getOption('card-exp-month') ?? '12',
            'cvc' => $input->getOption('card-cvv') ?? '123',
        ]);

        try {
            $response = $this->paymentProcessor->processPayment($gateway, $amount, $currency, $cardDetails);
            $output->writeln(json_encode($response, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logger->error('Payment command failed', ['error' => $e->getMessage()]);
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}