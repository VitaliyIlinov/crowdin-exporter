<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\Jobs;

use CrowdinApiClient\Crowdin;
use CrowdinApiClient\Model\SourceString;

final class AddSourceStringJob
{
    private Crowdin $crowdin;

    private int $projectId;

    private int $fileId;

    private string $identifier;

    private string $text;

    public function __construct(int $fileId, string $identifier, string $text)
    {
        $this->crowdin = app(Crowdin::class);
        $this->projectId = config('crowdin-exporter.project_id');
        $this->fileId = $fileId;
        $this->identifier = $identifier;
        $this->text = $text;
    }

    public function handle(): SourceString
    {
        return $this->crowdin->sourceString->create($this->projectId, [
            'fileId' => $this->fileId,
            'identifier' => $this->identifier,
            'text' => $this->text,
        ]);
    }
}
