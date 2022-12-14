<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserTest extends ApiTestCase
{

    use RefreshDatabaseTrait;

    public function testGetListOfDefaultUsers(): void
    {
        $url = 'api/users';
        $response = static::createClient()->request(method: 'GET', url: $url);
        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'hydra:totalItems' => 2
        ]);
        $this->assertCount(2, $json['hydra:member']);
    }
}
