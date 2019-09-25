<?php

/*
 * This file is part of ssx/skrub
 *
 *  (c) Scott Robinson <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
