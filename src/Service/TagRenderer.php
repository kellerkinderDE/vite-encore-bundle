<?php

declare(strict_types=1);

namespace K10r\ViteEncoreBundle\Service;

class TagRenderer
{
    public function renderScript(string $fileName): string
    {
        return sprintf(
            '<script type="module" src="%s"></script>',
            $fileName
        );
    }

    public function renderStylesheet(string $fileName): string
    {
        return sprintf(
            '<link rel="stylesheet" href="%s"/>',
            $fileName
        );
    }
}
