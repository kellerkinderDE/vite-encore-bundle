<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\Service;

use JsonException;

class EntrypointRenderer
{
    private string $basePath;
    private string $serverUrl;
    private bool $isDevServerEnabled;
    private string $environment;
    private ManifestParser $manifestParser;
    private TagRenderer $tagRenderer;

    private bool $hasRenderedViteClient = false;

    public function __construct(
        string $basePath,
        string $serverUrl,
        bool $isDevServerEnabled,
        string $environment,
        ManifestParser $manifestParser,
        TagRenderer $tagRenderer
    ) {
        $this->basePath           = $basePath;
        $this->serverUrl          = $serverUrl;
        $this->isDevServerEnabled = $isDevServerEnabled;
        $this->environment        = $environment;
        $this->manifestParser     = $manifestParser;
        $this->tagRenderer        = $tagRenderer;
    }

    /**
     * Fetches the script files from the manifest and returns them as HTML
     * script tags.
     * If the dev server is used, the vite client middleware for hot reloading
     * is also injected, if it is not present already.
     *
     * @throws JsonException
     */
    public function renderScripts(string $entryName): string
    {
        $scriptFiles = [];

        if (!$this->useServer()) {
            $scriptFiles = $this->manifestParser->getScriptFiles($entryName);
        } else {
            if (!$this->hasRenderedViteClient) {
                $scriptFiles[]               = '@vite/client';
                $this->hasRenderedViteClient = true;
            }

            $scriptFiles[] = $entryName;
        }

        $scriptTags = array_map(function ($scriptFile) {
            return $this->tagRenderer->renderScript(
                $this->prefixFile($scriptFile)
            );
        }, $scriptFiles);

        return implode(PHP_EOL, $scriptTags);
    }

    /**
     * Fetches the style files from the manifest and returns them as HTML link
     * tags.
     * If the dev server is used, no link tags are returned, since they
     * are injected by vite at runtime.
     *
     * @throws JsonException
     */
    public function renderStyles(string $entryName): string
    {
        if ($this->useServer()) {
            return '';
        }

        $styleFiles = $this->manifestParser->getStyleFiles($entryName);
        $linkTags   = array_map(function ($scriptFile) {
            return $this->tagRenderer->renderStylesheet(
                $this->prefixFile($scriptFile)
            );
        }, $styleFiles);

        return implode(PHP_EOL, $linkTags);
    }

    /**
     * Fetches the script imports from the manifest and returns them as HTML
     * module preloads.
     * If the dev server is used, no preloading tags are returned, since they
     * are not required for development.
     *
     * @throws JsonException
     */
    public function renderPreloads(string $entryName): string
    {
        if ($this->useServer()) {
            return '';
        }

        $importFiles = $this->manifestParser->getScriptImports($entryName);
        $preloadTags = array_map(function ($scriptFile) {
            return $this->tagRenderer->renderPreload(
                $this->prefixFile($scriptFile)
            );
        }, $importFiles);

        return implode(PHP_EOL, $preloadTags);
    }

    /**
     * Prefixes a file name with either the dev server URL or the base path,
     * depending on whether the dev server is used.
     */
    protected function prefixFile(string $fileName): string
    {
        $prefix = $this->useServer()
            ? $this->serverUrl . $this->basePath
            : $this->basePath;

        return sprintf('%s%s', $prefix, $fileName);
    }

    protected function isProduction(): bool
    {
        return $this->environment === 'prod';
    }

    protected function useServer(): bool
    {
        return $this->isDevServerEnabled && !$this->isProduction();
    }
}
