<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ManifestParser
{
    private string $manifestPath;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    public function getScriptFiles(string $entryName): array
    {
        $manifest = $this->parseManifest();

        if ($manifest === null || !array_key_exists($entryName, $manifest)) {
            return [];
        }

        return [
            $manifest[$entryName]['file'],
        ];
    }

    public function getStyleFiles(string $entryName): array
    {
        $manifest = $this->parseManifest();

        if (
            $manifest === null
            || !array_key_exists($entryName, $manifest)
            || !array_key_exists('css', $manifest[$entryName])
        ) {
            return [];
        }

        return $manifest[$entryName]['css'];
    }

    protected function parseManifest(): ?array
    {
        if (!file_exists($this->manifestPath)) {
            throw new FileNotFoundException($this->manifestPath);
        }

        $contents = file_get_contents($this->manifestPath);

        return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    }
}
