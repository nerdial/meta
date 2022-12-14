<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ItemTest extends ApiTestCase
{

//    use RefreshDatabaseTrait;

    public function testCreateNewItem(): void
    {

        $url = '/api/items';
        $newItem = [
            'title' => 'first item',
            'user' => '/api/users/1'
        ];
        static::createClient()->request(method: 'POST', url: $url, options: [
            'json' => $newItem
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => $newItem['title']
        ]);
    }


    public function testCreateNewItemAndThenCreateAuction(): void
    {

        $itemUrl = '/api/items';
        $auctionUrl = '/api/auctions';
        $newItem = [
            'title' => 'second item',
            'user' => '/api/users/1'
        ];
        $res = static::createClient()->request(method: 'POST', url: $itemUrl, options: [
            'json' => $newItem
        ]);

        $itemId = $res->toArray()['@id'];

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => $newItem['title']
        ]);

        $res = static::createClient()->request(method: 'GET', url: $itemUrl);

        $newAuction = [
            'price' => 1000,
            'item' => $itemId
        ];

        static::createClient()->request(method: 'POST', url: $auctionUrl, options: [
            'json' => $newAuction
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains($newAuction);

    }

}
