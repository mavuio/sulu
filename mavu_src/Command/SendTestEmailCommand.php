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

class SendTestEmailCommand extends Command
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'mavu:send:testmail';

    protected static $defaultDescription = 'sends testmail';

    /**
     * @var MailerInterface 
     */
    private $mailer;




    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

        parent::__construct();
    }


    protected function configure(): void
    {
        $this->addArgument('sender', InputArgument::REQUIRED, 'The email to send from');
        $this->addArgument('recipient', InputArgument::REQUIRED, 'The email to send to');
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->sendTestEmail($input->getArgument('sender'), $input->getArgument('recipient'));

        return Command::SUCCESS;
    }

    public function sendTestEmail($sender, $recipient)
    {
        $email = (new Email())
            ->from($sender)
            ->to($recipient)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Test email from sulu-cms')
            ->text('test email from sulu cms')
            ->html('<p>test email from <b>sulu</b>-cms</p>');

        return $this->mailer->send($email);

        // ...
    }
}
