<?php

namespace App\EventSubscriber;

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Handler\BlockchainHandler;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ItemSubscriber implements EventSubscriberInterface
{


    public function __construct(private readonly BlockchainHandler $handler,
                                private EntityManagerInterface     $manager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['createItemInBlockchain', EventPriorities::POST_WRITE],
        ];
    }

    public function createItemInBlockchain(ViewEvent $event): void
    {
        $item = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$item instanceof Item || Request::METHOD_POST !== $method) {
            return;
        }

        $connector = $this->handler->getConnector();

        $ethData = $connector->mintNft(
            title: $item->getTitle(),
            description: $item->getDescription(),
            image: $item->getDescription()
        );

        $item->setMetadata($ethData);

        $objectManger = $this->manager;
        $objectManger->persist($item);
        $objectManger->flush();
    }
}