<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ItemTest extends ApiTestCase
{

    use RefreshDatabaseTrait;

    public function testCreateNewItem(): void
    {

        $url = 'api/items';
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


}
