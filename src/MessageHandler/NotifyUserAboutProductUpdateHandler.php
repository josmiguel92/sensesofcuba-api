<?php


namespace App\MessageHandler;


use App\Entity\User;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use App\Message\Events\NotifyUserAboutProductUpdate;
use Symfony\Component\Routing\RouterInterface;

class NotifyUserAboutProductUpdateHandler implements MessageHandlerInterface
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
    /**
     * @var RouterInterface
     */
    private $router;


    public function __construct(MailerInterface $mailer, UserRepository $userRepository,
                                SocProductRepository $productRepository, RouterInterface $router)
    {
//        $this->userId = $userId;
//
//        $this->productId = $productId;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->router = $router;
    }

    public function __invoke(NotifyUserAboutProductUpdate $message)
    {
        $user = $this->userRepository->find($message->getUserId());
        $product = $this->productRepository->find($message->getProductId());
       
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
                'product_updated' => $product->getUpdatedAt()->format('M j, H:i'),
                'action_url' => $this->router->generate('homepage', [], 0 ) ,
            ])
          ->priority(Email::PRIORITY_NORMAL);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }
    }


}