<?php

namespace etobi\extensionUtils\Proxy;

use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

/**
 * a proxy class that can log to the OutputInterface of Symfony Console through
 * the PSR Logger Interface.
 */
class ConsoleOutputLoggerProxy implements LoggerInterface {

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(OutputInterface $output) {
        $this->output = $output;
    }


    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        if($this->output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            $this->output->writeln('<error>' . $message. '</error>');
        }
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        if($this->output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            $this->output->writeln('<error>' . $message. '</error>');
        }
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        if($this->output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            $this->output->writeln('<error>' . $message. '</error>');
        }
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        if($this->output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            $this->output->writeln('<error>' . $message. '</error>');
        }
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        if($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('<info>' . $message. '</info>');
        }
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        if($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln((string)$message);
        }
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        if($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln('<comment>' . $message. '</comment>');
        }
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        if($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $this->output->writeln('<comment>' . $message. '</comment>');
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if($level == 'emergency') {
            $this->emergency($message, $context);
        } elseif($level == 'alert') {
            $this->alert($message, $context);
        } elseif($level == 'critical') {
            $this->critical($message, $context);
        } elseif($level == 'error') {
            $this->error($message, $context);
        } elseif($level == 'warning') {
            $this->warning($message, $context);
        } elseif($level == 'notice') {
            $this->notice($message, $context);
        } elseif($level == 'info') {
            $this->info($message, $context);
        } elseif($level == 'debug') {
            $this->debug($message, $context);
        }
    }
}