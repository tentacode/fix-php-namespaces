<?php

declare(strict_types=1);

namespace Tentacode;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

class FixNamespaceCommand
{
    private $output;
    private $input;
    private $questionHelper;
    private $fileRepository;
    private $fileParser;
    
    private $workingDirectory;
    private $sourceDirectory;
    
    public function __construct(
        QuestionHelper $questionHelper,
        FileRepository $fileRepository,
        FileParser $fileParser
    ) {
        $this->questionHelper = $questionHelper;
        $this->fileRepository = $fileRepository;
        $this->fileParser = $fileParser;
    }
    
    public function __invoke(
        OutputInterface $output,
        InputInterface $input,
        ?string $workingDirectory
    )
    {
        $this->output = $output;
        $this->input = $input;
        $this->workingDirectory = $this->fileRepository->getRealDirectoryPath($workingDirectory ?? '.');
        $this->alias = $this->getSourceDirectoryAlias();
        
        $output->writeln('<info>PHP namespaces fixer</info>');
        
        $question = new Question('Where are your php source files ? <comment>[src]</comment> ', 'src');
        $this->sourceDirectory = $this->fileRepository->getSubDirectory(
            $this->workingDirectory,
            $this->questionHelper->ask($input, $output, $question)
        );
        
        $sourceFiles = $this->fileRepository->getPhpFiles($this->sourceDirectory);
        if (empty($sourceFiles)) {
            $output->writeln(sprintf(
                '<comment>There is no php files in source directory "%s"</comment>',
                $this->workingDirectory
            ));
            
            return;
        }
        
        $output->writeln(sprintf('%s PHP file(s) found in source directory.', count($sourceFiles)));
        
        $wrongNamespaces = $this->getWrongNamespaces($sourceFiles);
        if (empty($wrongNamespaces)) {
            $output->writeln('<info>No namespace issue in the source files!</info>');
            
            return;
        }
    
        $output->writeln(sprintf('<info>%s file(s) with a wrong namespace fixed!</info>', count($wrongNamespaces)));
        
        $allPhpFiles = $this->fileRepository->getPhpFiles($this->workingDirectory);
        $this->replaceNamespacesOccurences($allPhpFiles, $wrongNamespaces);
    }
    
    protected function replaceNamespacesOccurences(iterable $files, array $namespaces)
    {
        foreach ($files as $fileInfo) {
            
            $file = $fileInfo->openFile('r+');
            $fileContent = '';
            $changed = false;
            
            while (!$file->eof()) {
                $line = $file->fgets();
                
                foreach ($namespaces as $actualNamespace => $newNamespace) {
                    if (strpos($line, $actualNamespace) !== false) {
                        $fixedLine = str_replace($actualNamespace, $newNamespace, $line);
                        
                        $this->output->writeln("----------");
                        $this->output->writeln(sprintf(
                            'Namespace issue found in <comment>%s</comment>',
                            str_replace($this->workingDirectory, '', $fileInfo->getPathname())
                        ));
                        $this->output->writeln(sprintf(
                            '<error>- %s</error>',
                            trim($line)
                        ));
                        $this->output->writeln(sprintf(
                            '<info>+ %s</info>',
                            trim($fixedLine)
                        ));
                        
                        $changed = true;
                        
                        $line = $fixedLine;
                    }
                }
                
                $fileContent .= $line;
            }
            
            if ($changed) {
                file_put_contents($fileInfo->getRealPath(), $fileContent);
            }
        }
    }
    
    protected function getWrongNamespaces(iterable $files): iterable
    {
        $wrongNamespaces = [];
        
        foreach ($files as $file) {
            $actualNameSpace = $this->fileParser->getActualNamespace($file);
            if ($actualNameSpace === null) {
                continue;
            }
            
            $expectedNameSpace = $this->fileParser->getExpectedNamespace($file, $this->sourceDirectory, $this->alias);
            
            if ($actualNameSpace !== $expectedNameSpace) {
                $wrongNamespaces[$actualNameSpace] = $expectedNameSpace;
                
                $this->output->writeln(sprintf(
                    'Invalid namespace in <comment>%s</comment>',
                    str_replace($this->sourceDirectory,'', $file->getPathname())
                ));
                $this->output->writeln(sprintf(
                    'Actual:   <error>%s</error>',
                    $actualNameSpace
                ));
                $this->output->writeln(sprintf(
                    'Expected: %s',
                    $expectedNameSpace
                ));
                
                $this->fileRepository->changeNamespace($file, $expectedNameSpace);
            }
        }
        
        // sorting by actual namespace (inversed) to prevent search/replace errors
        krsort($wrongNamespaces);
        
        return $wrongNamespaces;
    }
    
    protected function getSourceDirectoryAlias()
    {
        return 'BetaSeries\\Insights\\';
    }
}
