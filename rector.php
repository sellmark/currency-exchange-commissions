<?php

use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    // Specify paths to PHP files
    $rectorConfig->paths([__DIR__ . '/src']);

    // Define PHP version to target
    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    // Set up rules: upgrade to PHP 8.2
    $rectorConfig->import(SetList::PHP_72);
    $rectorConfig->import(SetList::PHP_73);
    $rectorConfig->import(SetList::PHP_74);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
    $rectorConfig->import(SetList::PHP_82);

    // Include rules for cleaning up dead code
    $rectorConfig->import(SetList::DEAD_CODE);

    // Optionally, you can set up more specific Rector rules here
    // e.g., $rectorConfig->ruleWithConfiguration(SomeSpecificRule::class, $someConfiguration);
};
