<?php

declare(strict_types=1);

namespace Tentacode;

class FileParser
{
    public function getActualNamespace(\SplFileInfo $fileInfo): ?string
    {
        $file = $fileInfo->openFile('r');
        $content = $file->fread($file->getSize());
        
        if (!preg_match('/namespace\\s([^;]+);/', $content, $matches)) {
            // no namespace found in file
            return null;
        }
        
        $classname = $fileInfo->getBasename('.php');
        
        return sprintf('%s\%s', $matches[1], $classname);
    }

    public function getExpectedNamespace(\SplFileInfo $fileInfo, string $directory, ?string $alias): string
    {
        $filepath = $fileInfo->getRealPath();
        $filepath = str_replace([$directory, '.php', '/'], ['', '', '\\'], $filepath);
        
        $namespace = sprintf(
            '%s\%s',
            rtrim($alias, '\\'),
            ltrim($filepath, '\\')
        );
        
        return $namespace;
    }
}
