<?php

namespace spec\Tentacode;

use Tentacode\FileRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileRepositorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FileRepository::class);
    }
}
