<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Jobs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Pointpay\CrowdinExporter\DTO\LangFileTranslationDto;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

final class GetLangFileItemList
{
    /**
     * @return Collection<LangFileTranslationDto>
     */
    public function handle(): Collection
    {
        $rows = collect();

        $files = File::glob(lang_path(config('app.locale') . '/*.php'));

        foreach ($files as $file) {
            $items = include $file;
            $group = pathinfo($file, PATHINFO_FILENAME);
            $lang = str_replace('_', '-', basename(dirname($file)));

            $iterator = new RecursiveIteratorIterator(
                new RecursiveArrayIterator($items),
                RecursiveIteratorIterator::SELF_FIRST
            );
            $path = [];
            foreach ($iterator as $key => $value) {
                $path[$iterator->getDepth()] = $key;
                if (!is_array($value)) {
                    $rows->push(
                        new LangFileTranslationDto(
                            group: $group,
                            key: implode('.', array_slice($path, 0, $iterator->getDepth() + 1)),
                            lang: $lang,
                            text: $value,
                        )
                    );
                }
            }
        }
        return $rows;
    }
}
