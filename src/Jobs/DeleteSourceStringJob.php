<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Jobs;

use CrowdinApiClient\Crowdin;
use CrowdinApiClient\Model\SourceString;

final class DeleteSourceStringJob
{
    private Crowdin $crowdin;

    private int $projectId;

    private int $fileId;

    private string $identifier;

    public function __construct(int $fileId, string $identifier)
    {
        $this->crowdin = app(Crowdin::class);
        $this->projectId = config('crowdin-exporter.project_id');
        $this->fileId = $fileId;
        $this->identifier = $identifier;
    }

    public function handle(): void
    {
        $stringList = $this->crowdin->sourceString->list($this->projectId, [
            'fileId' => $this->fileId,
            'scope' => 'identifier',
            'filter' => $this->identifier,
        ]);
        /**@var $sourceString SourceString */
        $sourceString = $stringList->offsetGet(0);
        $this->crowdin->sourceString->delete($this->projectId, $sourceString->getId());
    }
}
