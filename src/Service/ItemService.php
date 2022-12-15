<?php

namespace App\Service;

use App\Entity\Item;
use App\Handler\BlockchainHandler;
use Doctrine\ORM\EntityManagerInterface;

class ItemService
{
    public function __construct(
        private readonly BlockchainHandler      $handler,
        private readonly EntityManagerInterface $manager
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function createItemInBlockchain(Item $item): Item
    {
        $connector = $this->handler->getConnector();

        $ethData = $connector->mintNft(
            title: $item->getTitle(),
            description: $item->getDescription(),
            image: $item->getDescription()
        );

        $item->setMetadata($ethData);
        $this->manager->persist($item);
        $this->manager->flush();
        return $item;
    }
}