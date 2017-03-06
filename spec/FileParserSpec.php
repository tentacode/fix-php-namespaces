<?php

namespace spec\Tentacode;

use Tentacode\FileParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FileParser::class);
    }
}
