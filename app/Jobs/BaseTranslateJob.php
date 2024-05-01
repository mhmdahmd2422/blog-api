<?php

namespace App\Jobs;

use App\Services\TranslateAPI\TranslateService;
use Exception;

class BaseTranslateJob
{

    protected function translated(string $targetLangCode, string $text)
    {
        return app(TranslateService::class)->translator($targetLangCode, $text);
    }

    /**
     * @throws Exception
     */
    protected function makeTextList(): void
    {
        $textList = [];
        foreach ($this->model->translatables as $attribute) {
            if ($this->validModelAttribute($attribute)) {
                $textList[$attribute] = $this->model->$attribute;
            } else {
                throw new Exception('Translatable attribute is not present in this model');
            }
        }

        $this->textList = $textList;
    }

    protected function extractTextFromList(): array
    {
        $extractedValues = [];

        foreach ($this->textList as $value) {
            $extractedValues[] = $value;
        }

        return $extractedValues;
    }

    protected function validModelAttribute(string $attribute): bool
    {
        return !is_null($this->model->$attribute);
    }
}
