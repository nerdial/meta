<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ItemTest extends ApiTestCase
{

//   use RefreshDatabaseTrait;


    private string $seller = 'api/users/1';
    private string $buyer = '/api/users/2';

    private float $defaultWalletAmount = 100000;


    private function createItem(array $data): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $url = '/api/items';

        $request = static::createClient();

        return $request->request(method: 'POST', url: $url, options: [
            'json' => $data
        ]);
    }

    public function testCreateNewItem(): void
    {
        $newItem = [
            'title' => 'first item',
            'user' => $this->seller,
            'description' => 'first description',
        ];
        $this->createItem($newItem);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => $newItem['title'],
            'description' => $newItem['description']
        ]);
    }

    public function testCreateNewItemAndValidateMintedObject(): void
    {
        $newItem = [
            'title' => 'second item',
            'user' => $this->seller,
            'description' => 'second description'
        ];
        $data = $this->createItem($newItem);

        $this->assertResponseIsSuccessful();

        $blockchainData = $data->toArray()['metadata'];

        $this->assertNotNull($blockchainData);
        $this->assertEquals($blockchainData['title'], $newItem['title']);
        $this->assertEquals($blockchainData['description'], $newItem['description']);
        $this->assertNotNull($blockchainData['address']);
    }


}
