<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Jobs;

use CrowdinApiClient\Crowdin;
use CrowdinApiClient\Model\File;
use Illuminate\Support\Collection;

final class GetFileListJob
{
    private Crowdin $crowdin;

    private int $projectId;

    private int $limit;

    private array $params;

    public function __construct(int $limit = 500, array $params = [])
    {
        $this->crowdin = app(Crowdin::class);
        $this->projectId = config('crowdin-exporter.project_id');
        $this->limit = $limit;
        $this->params = $params;
    }

    /**
     * @return Collection<File>
     */
    public function handle(): Collection
    {
        $files = [];
        $end = false;
        $offset = 0;
        do {
            $currentFiles = $this->crowdin->file->list(
                $this->projectId,
                [
                    'offset' => $offset,
                    'limit' => $this->limit,
                ] + $this->params
            );
            $end = count($currentFiles) === 0;
            $offset += $this->limit;
            $files = array_merge($files, iterator_to_array($currentFiles->getIterator()));
        } while (!$end);

        return collect($files);
    }
}
