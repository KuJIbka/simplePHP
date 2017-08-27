<?php

namespace Main\Core;

use Main\Command\LangToJsonCommand;
use Symfony\Component\Console\Application;

class AppConsole extends App
{
    /** @var Application */
    protected $symfonyApp;

    public function __construct()
    {
        parent::__construct();
        $this->symfonyApp = new Application();
        $this->symfonyApp->add(new LangToJsonCommand());
    }

    public function run()
    {
        $this->symfonyApp->run();
    }
}
