<?php

namespace App\EventSubscriber;

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Handler\BlockchainHandler;
use App\Entity\Auction;
use App\Entity\Transaction;
use App\Service\AuctionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class BuyAuctionSubscriber implements EventSubscriberInterface
{
    public function __construct(
//        private readonly BlockchainHandler $handler,
//        private EntityManagerInterface     $manager,
        private AuctionService $auctionService
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['buyAuction', EventPriorities::PRE_WRITE],
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

        $this->auctionService->buyAuction($auction);

    }
}