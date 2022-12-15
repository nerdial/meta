<?php

namespace App\Handler;

class BlockchainHandler
{

    public function __construct(private readonly EthHandler $ethHandler)
    {
    }

    public function getConnector(): EthHandler
    {
        return $this->ethHandler->connect(
            apiUrl: $_ENV['ETH_API_URL'],
            privateKey: $_ENV['ETH_PRIVATE_KEY'],
            apiKey: $_ENV['ETH_API_KEY']
        );
    }

}