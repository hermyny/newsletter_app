<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Newsletter;
use App\Form\UserType;
use App\Form\NewsletterType;
use App\Message\SendNewsletterMessage;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/newsletter', name: 'newsletter_')]
class NewsletterController extends AbstractController
{
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }


    #[Route('/', name: 'home')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
     
        $user = new User;
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $token = hash('sha256', uniqid());
            $user->setValidationToken($token);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $email = (new TemplatedEmail() )
            ->from('mynewsletter@symfony.fr')
            ->to($user->getEmail())
            ->subject('Inscription à MyNewsletter!')
            ->context([
                compact('user', 'token'),
                'user' =>$user,
                'token' =>$token,
                'expiration_date' => new \DateTime('+7 days')
                ])
            ->htmlTemplate('emails/inscription.html.twig');
            try{
                $mailer->send($email);
            } catch(TransportExceptionInterface $error){
                echo $error;
                
            } 

            // $mailer->send($email);  
            // dd($mailer->send($email));
        
            $this->addFlash('message', 'Inscription en attente de validation');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('newsletter/index.html.twig', [
            'user' =>$user,
         
            'form' => $form->createView(),  
            
        ]);
    }

    

    #[Route("confirm/{id}/{token}", name:"confirm")]
     public function confirm(User $user, $token): Response
     {
        if($user->getValidationToken() !=$token){
            throw $this->createNotFoundException('Oups... Un problème est survenu.');
        }

        if ($user->isIsValid(true)) {
            // Redirection si le compte est déjà activé
            throw $this->createNotFoundException("Ce lien n'est plus actif.");
        }
        $user->setIsValid(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('message', 'Votre compte est bien activé.');

        return $this->redirectToRoute('app_home');

     }


     #[Route("/prepare", name:"prepare")]
     public function prepare(Request $request): Response
     {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();

            return $this->redirectToRoute('newsletter_list');
        }
     return $this->render('newsletter/prepare.html.twig', [
        'form' => $form->createView()
        ]);
    }

    #[Route("/list", name:"list")]
    public function list(NewsletterRepository $newsletter): Response
    {
        return $this->render('newsletter/list.html.twig', [
            'newsletters' => $newsletter->findAll()
        ]);
    }

    #[Route("/send/{id}", name:"send")]
    public function send(Newsletter $newsletter, MessageBusInterface $messageBus): Response
   {
        $subscribers = $newsletter->getCategory()->getSubscriber();
        foreach($subscribers as $subscriber) {
            if($subscriber->isIsValid()){
                $messageBus->dispatch(new SendNewsletterMessage ($subscriber->getId(), $newsletter->getId()));
            }
        }

        return $this->redirectToRoute('newsletter_list');
   }


   #[Route("/unsubscribe/{id}/{newsletter}/{token}", name:"unsubscribe")]
   public function unsubscribe(User $subscriber, Newsletter $newsletter, $token): Response
   {
        if($subscriber->getValidationToken() != $token){
            throw $this->createNotFoundException('Une erreur est survenue.');
        }

        if(count($subscriber->getCategories()) > 1){
            $subscriber->removeCategory($newsletter->getCategory());
            $this->entityManager->persist($subscriber);
        }else{
            $this->entityManager->remove($subscriber);
        }
        $this->entityManager->flush();

        $this->addFlash('message', 'La newsletter a bien été supprimée!');
        return $this->redirectToRoute('app_home');
   }
 


}
