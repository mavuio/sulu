<?php

namespace Mavu\Command;

use PHPCR\SessionInterface;
use Mavu\GlobalBundle\Core\DummyService;
use Mavu\GlobalBundle\Core\TwClassesCore;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Sulu\Component\DocumentManager\DocumentManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchClassesCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'mavu:fetch-classes';

    protected static $defaultDescription = 'fetches all classes used in blocks';
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

        $dummyStr = "max-w-[" . time() . "px]";

        $this->twClassesCore->writeClassFiles($dummyStr);
        return Command::SUCCESS;
    }
}
