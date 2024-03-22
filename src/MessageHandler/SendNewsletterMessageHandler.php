<?php
namespace App\MessageHandler;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Message\SendNewsletterMessage;
use App\Service\SendNewsletterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendNewsletterMessageHandler
{
    private EntityManagerInterface $entityManager;
    private SendNewsletterService $newsletterService;

    public function __construct(EntityManagerInterface $entityManager, SendNewsletterService $newsletterService)
    {
        $this->entityManager = $entityManager;   
        $this->newsletterService = $newsletterService;   
    }

    public function __invoke(SendNewsletterMessage $message)
    {
        // do something with your message
        $user = $this->entityManager->find(User::class, $message->getUserId());
        $newsletter = $this->entityManager->find(Newsletter::class, $message->getNewsId());

        // On vérifie qu'on a toutes les informations nécessaires
        if($newsletter !== null && $user !== null){
            $this->newsletterService->send($user, $newsletter);
        }
    }
}


