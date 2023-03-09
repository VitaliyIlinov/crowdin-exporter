<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter\DTO;

class LangFileTranslationDto
{
    public readonly string $group;

    public readonly string $key;

    public readonly string $lang;

    public readonly string $text;

    public function __construct(string $group, string $key, string $lang, string $text)
    {
        $this->group = $group;
        $this->key = $key;
        $this->lang = $lang;
        $this->text = $text;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
