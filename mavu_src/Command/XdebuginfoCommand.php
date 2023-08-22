<?php

namespace Mavu\Command;

use PHPCR\SessionInterface;
use Mavu\GlobalBundle\Core\DummyService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Sulu\Component\DocumentManager\DocumentManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class XdebuginfoCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'mavu:xdebuginfo';

    protected static $defaultDescription = 'runs xdebuginfo in sulu-context';

    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        xdebug_info();
        return Command::SUCCESS;
    }
}