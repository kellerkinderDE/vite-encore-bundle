<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\Service;

use JsonException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ManifestParser
{
    private string $manifestPath;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * Fetches and returns all script files for the respective entrypoint.
     *
     * @throws JsonException
     *
     * @return array<string>
     */
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

    /**
     * Fetches and returns all style files for the respective entrypoint.
     *
     * @throws JsonException
     *
     * @return array<string>
     */
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

    /**
     * Fetches and returns all import files for the respective entrypoint,
     * to make them usable in preloading tags.
     *
     * @throws JsonException
     *
     * @return array<string>
     */
    public function getScriptImports(string $entryName): array
    {
        $manifest = $this->parseManifest();

        if (
            $manifest === null
            || !array_key_exists($entryName, $manifest)
            || !array_key_exists('imports', $manifest[$entryName])
        ) {
            return [];
        }

        return $manifest[$entryName]['imports'];
    }

    /**
     * Parses and returns the manifest.json file, if it exists.
     *
     * @throws JsonException
     *
     * @return null|array<mixed>
     */
    protected function parseManifest(): ?array
    {
        if (!file_exists($this->manifestPath)) {
            throw new FileNotFoundException($this->manifestPath);
        }

        $contents = file_get_contents($this->manifestPath);

        if (!$contents) {
            return null;
        }

        return json_decode(
            $contents,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
