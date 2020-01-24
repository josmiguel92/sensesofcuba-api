<?php


namespace App\Message\Events;


use App\Entity\User;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class NotifyUserAboutProductUpdateHandler
{
    private $productId;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SocProductRepository
     */
    private $productRepository;


    public function __construct($userId, $productId, MailerInterface $mailer, UserRepository $userRepository, SocProductRepository $productRepository)
    {
        $this->userId = $userId;

        $this->productId = $productId;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
    }

    public function __invoke(NotifyUserAboutProductUpdate $message)
    {
        $user = $this->userRepository->find($this->userId);
        $product = $this->productRepository->find($this->productId);
       
        if(!$user || !$product)
            return;
        
        $email = new TemplatedEmail();

        /** @var User $user */
        $email->addTo($user->getEmail());
        
         $email
            ->subject('We updated one of yours subscribed products at Senses of Cuba')
            ->htmlTemplate('email/foundation/cases/product-notify.html.twig')
            ->context([
                'subject' => 'New account on Senses of Cuba',
                'username' =>  $user->getName(),
                'product_name' => $product->getReferenceName(),
                'product_updated' => $product->getUpdatedAt()->format('r'),
                'action_url' => $this->router->generate('homepage', [], 0 ) ,
            ])
          ->priority(Email::PRIORITY_HIGH);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }
    }


}