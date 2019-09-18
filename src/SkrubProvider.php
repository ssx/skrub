<?php
namespace SSX\Package\Skrub;

use Composer\Plugin\Capability\CommandProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class SkrubProvider implements CommandProvider
{
    public function getCommands()
    {
        return [
            new SkrubCommand
        ];
    }
}
