<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Jobs;

use CrowdinApiClient\Crowdin;
use CrowdinApiClient\Model\File;
use Illuminate\Support\Collection;
use Pointpay\CrowdinExporter\VirtualSplFileObject;

final class AddSourceFileJob
{
    private Crowdin $crowdin;

    private int $projectId;

    private string $filename;

    private Collection $items;

    public function __construct(string $filename, Collection $items)
    {
        $this->crowdin = app(Crowdin::class);
        $this->projectId = config('crowdin-exporter.project_id');
        $this->filename = $filename;
        $this->items = $items;
    }

    public function handle(): File
    {
        $storage = $this->crowdin->storage->create(
            new VirtualSplFileObject($this->filename, $this->items->toJson())
        );
        $file = $this->crowdin->file->create($this->projectId, [
            'storageId' => $storage->getId(),
            'name' => $this->filename,
        ]);
        $this->crowdin->storage->delete($storage->getId());

        return $file;
    }
}
