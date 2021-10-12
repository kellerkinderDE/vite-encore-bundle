<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\Service;

class EntrypointRenderer
{
    private string $basePath;
    private string $serverUrl;
    private bool $useServer;
    private ManifestParser $manifestParser;
    private TagRenderer $tagRenderer;

    public function __construct(
        string $basePath,
        string $serverUrl,
        bool $useServer,
        ManifestParser $manifestParser,
        TagRenderer $tagRenderer
    ) {
        $this->basePath       = $basePath;
        $this->serverUrl      = $serverUrl;
        $this->useServer      = $useServer;
        $this->manifestParser = $manifestParser;
        $this->tagRenderer    = $tagRenderer;
    }

    public function renderScripts(string $entryName): string
    {
        $scriptFiles = [];

        if (!$this->useServer) {
            $scriptFiles = $this->manifestParser->getScriptFiles($entryName);
        } else {
            $scriptFiles[] = '@vite/client';
            $scriptFiles[] = $entryName;
        }

        $scriptTags = array_map(function ($scriptFile) {
            return $this->tagRenderer->renderScript(
                $this->prefixFile($scriptFile)
            );
        }, $scriptFiles);

        return implode(PHP_EOL, $scriptTags);
    }

    public function renderStyles(string $entryName): string
    {
        if ($this->useServer) {
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

    protected function prefixFile(string $fileName): string
    {
        $prefix = $this->useServer ? $this->serverUrl : $this->basePath;

        return sprintf('%s%s', $prefix, $fileName);
    }
}
