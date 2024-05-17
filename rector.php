<?php

use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([__DIR__ . '/src']);
    $rectorConfig->phpVersion(PhpVersion::PHP_83);
    $rectorConfig->import(SetList::PHP_72);
    $rectorConfig->import(SetList::PHP_73);
    $rectorConfig->import(SetList::PHP_74);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
    $rectorConfig->import(SetList::PHP_82);
    $rectorConfig->import(SetList::PHP_83);
    $rectorConfig->import(SetList::DEAD_CODE);
};
