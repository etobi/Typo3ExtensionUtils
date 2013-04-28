<?php

namespace etobi\extensionUtils\Service;


use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Helper\ProgressHelper;

/**
 * show the progress of a file download on OutputInterface
 */
class FileSizeProgressBar {

    /**
     * @var ProgressHelper
     */
    protected $progressHelper;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * if start() method on $this->progressHelper was called
     * @var bool
     */
    protected $isStarted = FALSE;

    /**
     * if download seems to be finished
     * @var bool
     */
    protected $isFinished = FALSE;

    /**
     * @param ProgressHelper $progressHelper
     * @param OutputInterface $output
     */
    public function __construct(ProgressHelper $progressHelper, OutputInterface $output) {
        $this->progressHelper = $progressHelper;
        $this->output = $output;
    }

    /**
     * the callback method that should be called by CURL
     *
     * @param integer $downloadedSize
     * @param integer $totalSize
     */
    public function progressCallback($totalSize, $downloadedSize) {
        if($totalSize == 0) {
            // NOTE: for some reason the first call by CURL is with both parameters set to 0
            return;
        }
        if($this->isFinished) {
            return;
        }
        if(!$this->isStarted) {
            $this->start($totalSize);
        }

        $this->progressHelper->setCurrent($downloadedSize, TRUE);

        if($totalSize == $downloadedSize) {
            $this->output->writeln(''); // write newline
            // NOTE: the callback is called multiple times with the same totalSize and downloadedSize by CURL
            $this->isFinished = TRUE;
        }
    }

    /**
     * @param integer $totalSize
     */
    protected function start($totalSize) {
        $this->progressHelper->start($this->output, $totalSize);

        $this->isStarted = TRUE;
    }

}