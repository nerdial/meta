<?php

namespace App\Service;

use App\Entity\Auction;
use App\Entity\Item;
use App\Entity\Transaction;
use App\Entity\User;
use App\Handler\BlockchainHandler;
use Doctrine\ORM\EntityManagerInterface;

class AuctionService
{
    public function __construct(
        private readonly BlockchainHandler      $handler,
        private readonly EntityManagerInterface $manager
    )
    {
    }


    public function buyAuction(Auction $auction): Auction
    {
        $item = $auction->getItem();
        $seller = $item->getUser();
        $buyer = $auction->getBuyer();


        $this->decreaseBuyerWallet(
            buyer: $buyer, auction: $auction
        );

        $this->increaseSellerWallet(
            seller: $seller, auction: $auction
        );

        $this->createNewTransaction(
            buyer: $buyer, auction: $auction
        );


        $data = $this->moveItemInBlockchain(
            buyer: $buyer, seller: $seller, item: $item
        );

        $auction->setMetadata($data);

        $this->manager->persist($auction);


        $this->manager->flush();

        return $auction;

    }

    public function increaseSellerWallet(User $seller, Auction $auction): void
    {
        $remainedInWallet = $seller->getWalletAmount() + $auction->getPrice();
        $seller->setWalletAmount($remainedInWallet);
        $this->manager->persist($seller);
    }

    public function decreaseBuyerWallet(User $buyer, Auction $auction): void
    {
        $remainedInWallet = $buyer->getWalletAmount() - $auction->getPrice();
        $buyer->setWalletAmount($remainedInWallet);
        $this->manager->persist($buyer);
    }

    public function createNewTransaction(User $buyer, Auction $auction): Transaction
    {
        $transaction = new Transaction();
        $transaction->setAuction($auction);
        $transaction->setUser($buyer);
        $transaction->setAmount($auction->getPrice());
        $transaction->setCreatedAt(new \DateTime());
        $this->manager->persist($transaction);
        return $transaction;
    }


    public function moveItemInBlockchain(User $buyer, User $seller, Item $item): array
    {

        $connector = $this->handler->getConnector();

        $itemAddressInBlockchain = $item->getMetadata()['address'];

        return $connector->moveNft(
            senderWalletId: $seller->getWalletId(),
            receiverWalletId: $buyer->getWalletId(),
            itemAddress: $itemAddressInBlockchain
        );
    }


}