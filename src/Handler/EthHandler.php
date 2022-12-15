<?php

namespace App\Handler;

use Psr\Log\LoggerInterface;

class EthHandler
{


    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function connect(string $apiUrl, string $privateKey, string $apiKey): self
    {

        $this->logger->info('connected to blockchain');

        return $this;
    }


    public function mintNft(string $title, string $description, string $image): array
    {

        try {
            $this->logger->info('nft minted');
            return [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'address' => 'https://api.etherscan.io/tx/' . bin2hex(random_bytes(60))
            ];

        } catch (\Exception $exception) {
            $this->logger->error('Could not connect to eth blockchain.');
            throw new \Exception($exception);
        }

    }

    public function moveNft(
        string $senderWalletId,
        string $receiverWalletId,
        string $itemAddress): array
    {
        try {
            $this->logger->info('nft moved to new address');
            return [
                'seller' => $senderWalletId,
                'buyer' => $receiverWalletId,
                'oldAddress' => $itemAddress,
                'newAddress' => 'https://api.etherscan.io/tx/' . bin2hex(random_bytes(60))
            ];

        } catch (\Exception $exception) {
            $this->logger->error('Could not connect to eth blockchain.');
            throw new \Exception($exception);
        }

    }


}