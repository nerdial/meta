<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AuctionTest extends ApiTestCase
{

    private string $seller = '/api/users/1';
    private string $buyer = '/api/users/2';

    private float $defaultWalletAmount = 100000;

    private function getObject(string $url): array
    {
        $request = static::createClient();
        return $request->request(method: 'GET', url: $url)->toArray();
    }

    private function createAuction(array $data): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $url = '/api/auctions';
        return static::createClient()->request(method: 'POST', url: $url, options: [
            'json' => $data
        ]);
    }

    private function buyAuction(array $data, string $id): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $url = $id;
        return static::createClient()->request(method: 'PATCH', url: $url, options: [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/merge-patch+json'
            ]
        ]);
    }

    private function createItem(array $data): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $url = '/api/items';

        $request = static::createClient();

        return $request->request(method: 'POST', url: $url, options: [
            'json' => $data
        ]);
    }


    public function testCreateNewItemAndThenCreateAuction(): void
    {
        $newItem = [
            'title' => 'third item',
            'user' => $this->seller,
            'description' => 'third description'
        ];

        $res = $this->createItem($newItem);


        $itemId = $res->toArray()['@id'];


        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => $newItem['title']
        ]);
        $newAuction = [
            'price' => 1000,
            'item' => $itemId
        ];

        $data = $this->createAuction($newAuction);
        $json = $data->toArray();

        $this->assertNotNull($json["@id"]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'price' => $newAuction['price'],
            "@context" => "/api/contexts/Auction",
            "@type" => "Auction"
        ]);

    }

    public function testCreateNewItemAndThenCreateAuctionAndBuyIt(): void
    {
        $newItem = [
            'title' => 'fourth item',
            'user' => $this->seller,
            'description' => 'fourth description'
        ];

        $res = $this->createItem($newItem);

        $this->assertResponseIsSuccessful();

        $itemId = $res->toArray()['@id'];


        $auctionPrice = 5000;

        $newAuction = [
            'price' => $auctionPrice,
            'item' => $itemId
        ];

        $data = $this->createAuction($newAuction);

        $this->assertResponseIsSuccessful();

        $json = $data->toArray();
        $auctionId = $json["@id"];

        $buy = [
            'item' => $itemId,
            'buyer' => $this->buyer,
        ];

        $res = $this->buyAuction($buy, $auctionId);


        $this->assertResponseIsSuccessful();

        $json = $res->toArray();

        $metadata = $json['metadata'];

        $this->assertNotNull($metadata["seller"]);
        $this->assertNotNull($metadata["buyer"]);
        $this->assertNotNull($metadata["oldAddress"]);
        $this->assertNotNull($metadata["newAddress"]);

        $this->assertJsonContains([
            'price' => $auctionPrice,
            "@context" => "/api/contexts/Auction",
            "@type" => "Auction"
        ]);

        $seller = $this->getObject($this->seller);

        $buyer = $this->getObject($this->buyer);

        $remainingForSeller = $this->defaultWalletAmount + $auctionPrice;
        $remainingForBuyer = $this->defaultWalletAmount - $auctionPrice;

        $this->assertEquals($remainingForSeller, $seller['wallet_amount']);
        $this->assertEquals($remainingForBuyer, $buyer['wallet_amount']);

        $newTransactionId = $json['transaction'];

        $this->getObject($newTransactionId);

        $this->assertJsonContains([
            'amount' => $auctionPrice,
            "@context" => "/api/contexts/Transaction",
            "@id" => $newTransactionId,
            "@type" => "Transaction",
            'user' => $this->buyer
        ]);

    }

}
