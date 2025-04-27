<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BadWordApiService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Check if text contains profanity using an external API
     * 
     * @param string $text The text to check
     * @return array Response containing 'hasProfanity' and optionally 'badWords'
     */
    public function checkProfanity(string $text): array
    {
        try {
            // Using PurgoMalum API as an example - it's free and doesn't require authentication
            $response = $this->httpClient->request('GET', 'https://www.purgomalum.com/service/json', [
                'query' => [
                    'text' => $text
                ]
            ]);

            $data = $response->toArray();
            
            // Compare original and result to determine if bad words were found
            // PurgoMalum replaces bad words with asterisks
            $hasProfanity = $data['result'] !== $text;
            
            return [
                'hasProfanity' => $hasProfanity,
                'filteredText' => $data['result'],
                'success' => true
            ];
        } catch (\Exception $e) {
            // In case of API failure, log error but don't block submission
            return [
                'hasProfanity' => false,
                'success' => false,
                'error' => 'API request failed: ' . $e->getMessage()
            ];
        }
    }
} 