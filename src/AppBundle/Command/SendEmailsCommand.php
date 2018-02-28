<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Repository\CommentRepository;
use AppBundle\Repository\AdvertRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\Comment;
use AppBundle\Repository\Advert;
use AppBundle\Repository\User;

class SendEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('email:send-Emails')
        ->setDescription('Send emails for new comments.')
        ->setHelp('This command allows you to send an email...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Zaczynamy!',
            '==========',
            '',
        ]);
        
        $output->writeln('Sprawdzamy czy sa nowe komentarze');
        
        
        $commentRepository = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Comment');
        $mailer = $this->getContainer()->get('mailer');

                
        $allComments = $commentRepository->findByNotified(false);
      
        $groupByAdvert = [];
        
        foreach($allComments as $comment) {
            if($comment->getId() !== null) {
                $groupByUserAndAdvert[$comment->getAdvert()->getUser()->getId()][$comment->getAdvert()->getId()][] = $comment;
            }
        }
            $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
            $userRepository = $this->getContainer()->get('doctrine')->getRepository('AppBundle:User');
            $advertRepository = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Advert');
            
            foreach($groupByUserAndAdvert as $userId => $advertAndComments) {
                $text = '';
                foreach($advertAndComments as $advertId => $comments) {
               // $output->write($comment->getCommentContent());
                
                //$advert = $advertRepository->findOneById($advertId);
                $text .= "Masz ".count($comments). "nowych komentarzy do ogłoszenia o id ".$advertId.'</br>';
                    foreach($comments as $comment) {
                        $comment->setNotified(true);
                    }
                }
                $user = $userRepository->findOneById($userId);
                $message = (new \Swift_Message('Witaj '.$user->getUsername()))
                        ->setFrom($this->getContainer()->getParameter('mailer_user'))
                        ->setTo($user->getEmail())
                        ->setBody($text, 'text/html'
                        );
                $mailer->send($message);
            }
            $manager->flush();
        
        $output->writeln('Komentarze zostały wysłane');
    }
}
