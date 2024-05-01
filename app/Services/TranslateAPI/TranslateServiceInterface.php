<?php

namespace App\Services\TranslateAPI;

interface TranslateServiceInterface
{
    public function translator(string $targetLangCode, string $text): TranslateService;

    public function getText(): string;

    public function getSourceLangCode(): string;
}
