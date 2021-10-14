<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\Twig;

use K10r\ViteEncoreBundle\Service\EntrypointRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteEncoreExtension extends AbstractExtension
{
    private EntrypointRenderer $entrypointRenderer;

    public function __construct(
        EntrypointRenderer $entrypointRenderer
    ) {
        $this->entrypointRenderer = $entrypointRenderer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_script_entry', [
                $this, 'getScriptEntrypoint',
            ], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('vite_style_entry', [
                $this, 'getStyleEntrypoint',
            ], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('vite_preloads_entry', [
                $this, 'getPreloadsEntrypoint',
            ], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function getScriptEntrypoint(string $entryName): string
    {
        return $this->entrypointRenderer->renderScripts($entryName);
    }

    public function getStyleEntrypoint(string $entryName): string
    {
        return $this->entrypointRenderer->renderStyles($entryName);
    }

    public function getPreloadsEntrypoint(string $entryName): string
    {
        return $this->entrypointRenderer->renderPreloads($entryName);
    }
}
