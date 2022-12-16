<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserTest extends ApiTestCase
{

    use RefreshDatabaseTrait;

    private string $seller = '/api/users/1';
    private string $buyer = '/api/users/2';

    private function getObject(string $url): array
    {
        $request = static::createClient();
        return $request->request(method: 'GET', url: $url)->toArray();
    }

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

    public function testGetUsersById(): void
    {
        $this->getObject($this->seller);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/User",
            "@id" => $this->seller,
            "@type" => "User"
        ]);

        $this->getObject($this->buyer);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/User",
            "@id" => $this->buyer,
            "@type" => "User"
        ]);
    }


}
