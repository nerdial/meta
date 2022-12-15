<?php

namespace App\EventSubscriber;

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Handler\BlockchainHandler;
use App\Entity\Auction;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class BuyAuctionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BlockchainHandler $handler,
        private EntityManagerInterface     $manager
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['buyAuction', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * @throws \Exception
     */
    public function buyAuction(ViewEvent $event): void
    {
        $auction = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$auction instanceof Auction || Request::METHOD_PATCH !== $method) {
            return;
        }

        $item = $auction->getItem();
        $seller = $item->getUser();
        $buyer = $auction->getBuyer();


        $remainedInWallet = $buyer->getWalletAmount() - $auction->getPrice();
        $buyer->setWalletAmount($remainedInWallet);
        $this->manager->persist($buyer);


        $remainedInWallet = $seller->getWalletAmount() + $auction->getPrice();
        $seller->setWalletAmount($remainedInWallet);
        $this->manager->persist($seller);


        $transaction = new Transaction();

        $transaction->setAuction($auction);
        $transaction->setUser($buyer);
        $transaction->setAmount($auction->getPrice());
        $transaction->setCreatedAt(new \DateTime());


        $connector = $this->handler->getConnector();

        $itemAddressInBlockchain = $item->getMetadata()['address'];

        $data = $connector->moveNft(
            senderWalletId: $seller->getWalletId(),
            receiverWalletId: $buyer->getWalletId(),
            itemAddress: $itemAddressInBlockchain
        );


        $auction->setMetadata($data);

        $this->manager->persist($auction);

        $this->manager->persist($transaction);

        $this->manager->flush();

    }
}