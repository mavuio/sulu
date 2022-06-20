<?php

namespace Mavu\Command;

use Mavu\GlobalBundle\Core\DummyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mavu\GlobalBundle\Core\TwClassesCore;
use Sulu\Component\DocumentManager\DocumentManager;
use PHPCR\SessionInterface;
use Symfony\Component\Filesystem\Filesystem;

class RebuildPresetStylesheet extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'mavu:rebuild-presets';

    protected static $defaultDescription = 'rebuilds preset.css';
    /**
     * @var TwClassesCore
     */
    private $twClassesCore;




    public function __construct(TwClassesCore $twClassesCore)
    {
        $this->twClassesCore = $twClassesCore;

        parent::__construct();
    }


    protected function configure(): void
    {
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $content = ".dummy { border:1px solid red;} /* " . time() . " */";
        $this->twClassesCore->writeDekorStylesheet($content);

        return Command::SUCCESS;
    }
}
