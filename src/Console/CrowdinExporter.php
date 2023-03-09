<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Console;

use CrowdinApiClient\Model\File;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Pointpay\CrowdinExporter\DTO\LangFileTranslationDto;
use Pointpay\CrowdinExporter\Jobs\AddSourceFileJob;
use Pointpay\CrowdinExporter\Jobs\AddSourceStringJob;
use Pointpay\CrowdinExporter\Jobs\DeleteSourceStringJob;
use Pointpay\CrowdinExporter\Jobs\GetFileListJob;
use Pointpay\CrowdinExporter\Jobs\GetLangFileItemList;
use Pointpay\CrowdinExporter\Jobs\GetSourceStringsJob;

class CrowdinExporter extends Command
{
    protected $signature = 'pointpay-crowdin:export';

    protected $description = 'Export new strings to Crowdin service';

    protected $help = '<info>Export strings to Crowdin, if they do not exist on the service</info>';

    public function handle(): int
    {
        $this->info('Getting project file list...');
        $appStrings = (new GetLangFileItemList())->handle();
        $appFileList = $appStrings->keyBy('group')->keys();

        $this->info('Getting source project file list...');
        $crowdinFileList = (new GetFileListJob())->handle();
        $crowdinFileList = $crowdinFileList->filter(
            fn(File $file) => $appFileList->search(pathinfo($file->getName(), PATHINFO_FILENAME)) !== false
        );

        $this->info(sprintf('Downloading source app project files... items: %d', $crowdinFileList->count()));
        $sourceStrings = (new GetSourceStringsJob(fileList: $crowdinFileList))->handle();

        $this->info('Deleting mismatch items from Crowdin... ');
        $this->deleteStrings($sourceStrings, $appStrings, $crowdinFileList);

        $this->info('Export new items to Crowdin... ');
        $this->exportNewStrings($sourceStrings, $appStrings, $crowdinFileList);

        return 0;
    }

    private function exportNewStrings(Collection $sourceStrings, Collection $appStrings, Collection $crowdinFileList)
    {
        $newItems = $appStrings->filter(
            fn(LangFileTranslationDto $item) => $sourceStrings->where('group', $item->getGroup())
                ->where('key', $item->getKey())
                ->isEmpty()
        );
        $this->info(sprintf('New items: %d', $newItems->count()));

        $ignoreItems = collect();

        /** @var LangFileTranslationDto $item */
        foreach ($newItems as $key => $item) {
            if ($ignoreItems->search($key)) {
                continue;
            }
            $fileName = "{$item->getGroup()}.json";

            /** @var File $crowdinFile */
            $crowdinFile = $crowdinFileList->first(fn(File $item) => $item->getName() === $fileName);

            if ($crowdinFile === null) {
                $currentGroupItems = $newItems->where('group', $item->getGroup());
                $this->info(
                    sprintf('Export keys: %s to %s', $currentGroupItems->implode('key', ', '), $item->getGroup())
                );
                $crowdinFile = (new AddSourceFileJob(
                    filename: $fileName,
                    items: $currentGroupItems->pluck('text', 'key'),
                ))->handle();
                $ignoreItems->push(...$currentGroupItems->keys()->toArray());
                $crowdinFileList->add($crowdinFile);
                continue;
            }

            $this->info(sprintf('Export key: %s to %s', $item->getKey(), $fileName));

            (new AddSourceStringJob(
                fileId: $crowdinFile->getId(),
                identifier: $item->getKey(),
                text: $item->getText()
            ))->handle();
        }
    }

    private function deleteStrings(Collection $sourceStrings, Collection $appStrings, Collection $crowdinFileList): void
    {
        $mismatchItems = $sourceStrings->filter(
            fn(LangFileTranslationDto $item) => $appStrings->where('group', $item->getGroup())
                ->where('key', $item->getKey())
                ->isEmpty()
        );
        $this->info(sprintf('Mismatch items: %d', $mismatchItems->count()));

        /**@var $item LangFileTranslationDto */
        foreach ($mismatchItems as $item) {
            $fileName = "{$item->getGroup()}.json";

            /**@var File $crowdinFile */
            $crowdinFile = $crowdinFileList->first(fn(File $file) => $file->getName() === $fileName);
            $this->info(sprintf('Deleting key: %s from %s', $item->getKey(), $fileName));
            (new DeleteSourceStringJob(
                fileId: $crowdinFile->getId(),
                identifier: $item->getKey(),
            ))->handle();
        }
    }
}
