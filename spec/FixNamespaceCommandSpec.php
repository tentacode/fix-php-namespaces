<?php

namespace spec\Tentacode;

use Tentacode\FixNamespaceCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class FixNamespaceCommandSpec extends ObjectBehavior
{
    function let(OutputInterface $output, InputInterface $input)
    {
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FixNamespaceCommand::class);
    }
    
    function it_can_be_runned($output, $input)
    {
        $output->writeln('<info>🙃</info>')->shouldBeCalled();
        
        $this($output, $input);
    }
}
