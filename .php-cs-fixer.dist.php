<?php

require 'vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return K10r\Codestyle\PHP74::create()
    ->setFinder($finder);
