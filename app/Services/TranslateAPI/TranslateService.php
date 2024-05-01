<?php

namespace App\Services\TranslateAPI;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Translate\TranslateClient;

class TranslateService implements TranslateServiceInterface
{
    protected Result $result;

    public function __construct(protected TranslateClient $client)
    {
    }

    public function translator(string $targetLangCode, string $text): TranslateService
    {
        try {
            $this->result = $this->client->translateText([
                'SourceLanguageCode' => 'auto',
                'TargetLanguageCode' => $targetLangCode,
                'Text' => $text,
            ]);
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }

        return $this;
    }

    public function getText(): string
    {
        return $this->result->get('TranslatedText');
    }
    public function getSourceLangCode(): string

    {
        return $this->result->get('SourceLanguageCode');
    }
}
