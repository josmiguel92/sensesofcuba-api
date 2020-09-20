<?php


namespace App\MessageHandler;


use App\Entity\User;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
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

        $hiddenProducts = $user->getHiddenProducts();
        if($hiddenProducts && $hiddenProducts->contains($product)) {
            return;
        }


        $email = new TemplatedEmail();

        /** @var User $user */
        $email->addTo($user->getEmail());


        $productThumbnail = $product->hasImage() ? $product->getImage()->getThumbnailPath() : null;

         $email
            ->subject('We updated one of your subscribed products at Senses of Cuba Infonet')
            ->htmlTemplate('email/abacus/cases/product-notify.html.twig')
            ->context([
                'subject' => 'We updated one of your subscribed products at Senses of Cuba Infonet',
                'username' =>  $user->getName(),
                'product_name' => $product->getTranslatedNameOrReference('en'),
                'product_thumb' => $productThumbnail,
                'product_desc' => $product->translate()->getDescription(),
//                'product_updated' => $product->getUpdatedAt()->format('M j, H:i'),
                'action_url' => $this->router->generate('homepage', [], 0 ),
                'unsubscribe_url' => $this->router->generate('products_updates_unsubscribe', [
                    'email' => $user->getEmail(),
                    'token' => $user->getStaticUserHash()
                ], 0 ),
            ])
          ->priority(Email::PRIORITY_NORMAL);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }catch (HandlerFailedException  $e) {
            return;
        }
    }


}
