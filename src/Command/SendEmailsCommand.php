<?php


namespace App\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendEmailsCommand extends Command
{
    private $entityManager;
    private $mailer;

    protected static $defaultName = 'app:send-emails';

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Sends notification emails to users.')
            ->setHelp('This command goes through all team events and sends emails to all team members if the event is about to start in the next 30 minutes.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventRepository = $this->entityManager->getRepository('App:Event');
        $events = $eventRepository->getPendingNotNotifiedEvents();
        if(count($events))
        {
            foreach($events as $event)
            {
                $now = new \DateTime();
                $timeToStart = $now->diff($event->getStartTime());
                $yearsToStart = $timeToStart->y;
                $monthsToStart = $timeToStart->m;
                $daysToStart = $timeToStart->d;
                $hoursToStart = $timeToStart->h;
                $minutesToStart = $timeToStart->i;

                if($minutesToStart <= 30 && $minutesToStart > 0 && $hoursToStart == 0 && $daysToStart == 0 && $monthsToStart == 0 && $yearsToStart == 0)
                {
                    $memberEmails = [];
                    if(count($event->getTeam()->getMembers()))
                    {
                        foreach ($event->getTeam()->getMembers() as $member)
                        {
                            $memberEmails []= $member->getEmail();
                        }
                        $email = (new Email())
                            ->from($event->getTeam()->getLeader()->getEmail())
                            ->to(...$memberEmails)
                            ->subject('Event ' . $event->getName() . ' is starting soon!')
                            ->html('<p>Team leader ' . $event->getTeam()->getLeader() . ' is summoning you and the rest of ' . $event->getTeam() . ' to an event that is taking place between ' . $event->getStartTime()->format('d/m/Y H:i') . ' and ' . $event->getEndTime()->format('d/m/Y H:i') . '.</p>');
                        try {
                            $this->mailer->send($email);
                        } catch (TransportExceptionInterface $e) {
                            return Command::FAILURE;
                        }
                    }
                    else
                    {
                        return Command::SUCCESS;
                    }
                }
            }
        }
        else{
            return Command::SUCCESS;
        }
        return Command::SUCCESS;
    }
}