<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Jobs;

use CrowdinApiClient\Crowdin;
use CrowdinApiClient\Model\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pointpay\CrowdinExporter\DTO\LangFileTranslationDto;

final class GetSourceStringsJob
{
    private Crowdin $crowdin;

    private int $projectId;

    /**@var Collection<File> $fileList */
    private Collection $fileList;

    public function __construct(Collection $fileList)
    {
        $this->crowdin = app(Crowdin::class);
        $this->projectId = config('crowdin-exporter.project_id');
        $this->fileList = $fileList;
    }

    /**
     * @return Collection<LangFileTranslationDto>
     */
    public function handle(): Collection
    {
        $rows = collect();

        foreach ($this->fileList as $file) {
            $download = $this->crowdin->file->download($this->projectId, $file->getId());
            $group = pathinfo($file->getName(), PATHINFO_FILENAME);
            $content = collect(Arr::dot(Http::get($download->getUrl())->json()));
            $rows = $rows->merge(
                $content->map(function ($sourceString, $key) use ($group, $file) {
                    return new LangFileTranslationDto(
                        group: $group,
                        key: $key,
                        lang: config('app.locale'),
                        text: $sourceString,
                    );
                })->values()
            );
        }
        return $rows;
    }
}
