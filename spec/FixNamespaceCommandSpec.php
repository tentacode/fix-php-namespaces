<?php

namespace spec\Tentacode;

use Tentacode\FixNamespaceCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Tentacode\FileRepository;
use Tentacode\FileParser;

class FixNamespaceCommandSpec extends ObjectBehavior
{
    function let(
        OutputInterface $output,
        InputInterface $input,
        QuestionHelper $questionHelper,
        FileRepository $fileRepository,
        FileParser $fileParser
    )
    {
        $this->beConstructedWith($questionHelper, $fileRepository, $fileParser);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FixNamespaceCommand::class);
    }
    
    function it_can_be_runned($output, $input)
    {
        $output->writeln('<info>PHP namespaces fixer</info>')->shouldBeCalled();
        
        $this($output, $input, null);
    }
}
