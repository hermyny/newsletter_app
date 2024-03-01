<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;

class NewsletterController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}

    #[Route('/newsletter', name: 'app_newsletter')]
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

            $email = (new TemplatedEmail())
            ->from('mynewsletter@symfony.fr')
            ->to($user->getEmail())
            ->subject('Inscription à MyNewsletter!')
            ->text('Sending emails is fun again!')
            ->context([
                compact('user', 'token'),
                'user' => $user,
                'expiration_date' => new \DateTime('+7 days')
                ])
            ->htmlTemplate('emails/inscription.html.twig');

        $mailer->send($email);  
        
        $this->addFlash('message', 'Inscription en attente de validation');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('newsletter/index.html.twig', [
            'form' => $form->createView(),
            'user' =>$user,
            
        ]);
    }
}
