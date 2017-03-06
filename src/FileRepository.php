<?php

declare(strict_types=1);

namespace Tentacode;

use Symfony\Component\Finder\Finder;

class FileRepository
{
    public function getPhpFiles(string $directory): iterable
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf(
                'Directory "%s" is not a valid directory.'
            ));
        }
        
        $finder = new Finder();
        
        return $finder->files()->name('*.php')->in($directory)->exclude('vendor');
    }
    
    public function getRealDirectoryPath(string $directory): string
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid directory.', $directory));
        }
        
        return realpath($directory);
    }
    
    public function getSubDirectory(string $workingDirectory, string $subDirectory): string
    {
        $fullDirectory = sprintf(
            '%s/%s', 
            rtrim($workingDirectory, '/'),
            ltrim($subDirectory, '/')
        );
        
        if (is_dir($fullDirectory)) {
            return realpath($fullDirectory);
        } 
        
        if (is_dir($subDirectory)) {
            return realpath($subDirectory);
        }
        
        throw new \InvalidArgumentException(sprintf('"%s" is not a valid directory.', $subDirectory));
    }
    
    public function changeNamespace(\SplFileInfo $file, string $namespace)
    {
        $filepath = $file->getRealPath();
        $content = file_get_contents($filepath);
        
        if (!preg_match('/^(.*)\\\\([^\\\\]+)$/', $namespace, $matches)) {
            throw new \RuntimeException(sprintf(
                'Cannot extract short namespace from namespace "%s"',
                $namespace
            ));
        }
        
        $shortNamespace = $matches[1];
        
        $newContent = preg_replace(
            '/namespace\\s[^;]+;/',
            sprintf('namespace %s;', $shortNamespace),
            $content
        );
        
        file_put_contents($filepath, $newContent);
    }
}
