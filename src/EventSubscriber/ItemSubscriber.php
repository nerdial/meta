<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Item;
use App\Service\ItemService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ItemSubscriber implements EventSubscriberInterface
{


    public function __construct(private readonly ItemService $itemService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['createItemInBlockchain', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * @throws \Exception
     */
    public function createItemInBlockchain(ViewEvent $event): void
    {
        $item = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$item instanceof Item || Request::METHOD_POST !== $method) {
            return;
        }

        $this->itemService->createItemInBlockchain(
            item: $item
        );
    }
}