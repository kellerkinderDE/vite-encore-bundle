<?php

require 'vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return K10r\Codestyle\PHP71::create()
    ->setFinder($finder);
