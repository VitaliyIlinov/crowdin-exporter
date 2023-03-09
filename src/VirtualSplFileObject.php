<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter;

use SplFileInfo;

class VirtualSplFileObject extends SplFileInfo
{
    private string $name;

    private string $content;

    private string $tempFilePath;

    public function __construct(string $name, string $content)
    {
        $this->name = $name;
        $this->content = $content;
        $this->tempFilePath = tempnam(sys_get_temp_dir(), 'TMP_');
        file_put_contents($this->tempFilePath, $this->content);
    }

    public function getFilename(): string
    {
        return $this->name;
    }

    public function getRealPath(): string
    {
        return $this->tempFilePath;
    }
}
