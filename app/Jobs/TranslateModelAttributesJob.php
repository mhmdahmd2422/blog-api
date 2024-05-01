<?php

namespace App\Jobs;

use Aws\Comprehend\ComprehendClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateModelAttributesJob extends BaseTranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $textList;

    public function __construct(protected Model $model, protected string $currentRequestLocale)
    {
        //
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        $this->makeTextList();

        if ($this->textLangIsCurrentLocale()) {
            foreach ($this->translateLocales() as $locale) {
                $this->model->withoutEvents(function () use ($locale) {
                    $this->model->translationModel()->create([
                        'post_id' => $this->model->id,
                        'locale' => $locale,
                        ...$this->translatedTextList($locale)
                    ]);
                });
            }
        }
    }

    protected function translatedTextList(string $targetLangCode): array
    {
        $translatedTextList = [];

        foreach ($this->textList as $attribute => $value) {
            $translatedTextList[$attribute] = $this->translated($targetLangCode, $value)->getText();
        }

        return $translatedTextList;
    }

    protected function textLangIsCurrentLocale(): bool
    {
        $result = app(ComprehendClient::class)
            ->batchDetectDominantLanguage(['TextList' => $this->extractTextFromList()]);

        if ($result['ResultList'][0]['Languages'][0]['LanguageCode'] === $this->currentRequestLocale) {
            return true;
        }

        return false;
    }

    protected function translateLocales(): array
    {
        return array_diff(config('localization.supportedLocales'), [$this->currentRequestLocale]);
    }
}
