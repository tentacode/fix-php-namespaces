<?php

declare(strict_types=1);

namespace Tentacode;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class FixNamespaceCommand
{
    public function __invoke(OutputInterface $output, InputInterface $input)
    {
        $output->writeln('<info>🙃</info>');
    }
}
