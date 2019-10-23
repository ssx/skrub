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

use RecursiveIteratorIterator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SkrubCommand
 *
 * @package SSX\Package\Skrub
 */
class SkrubCommand extends \Composer\Command\BaseCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('skrub')->setDescription('Free up some space from your installed Composer packages.');
        $this->addOption('perform', null, InputOption::VALUE_NONE, 'Perform the actual deletion of files.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeLn(' ');
        
        // An array of directories that we're happy to remove.
        $directories_to_remove = [
            'test', 'tests',
            'fixture', 'fixtures',
            'stub',
            'doc', 'docs',
            'example', 'examples',
            'docker',
            'other'
        ];


        // Build a path to the vendor folder.
        $vendor = dirname(\Composer\Factory::getComposerFile()).'/vendor/';

        // A couple of variables to hold useful things.
        $directories_to_action = [];
        $total_removeable = 0;

        // Find all files in the vendor directory and then test to see if they're
        // something we can remove.
        $iterator = new RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $vendor,
                \FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        // Iterate and find things to nuke.
        foreach ($iterator as $path) {
            $full_path = $path->getPathName();
            $name = $path->getFileName();

            // Is the given path a directory? and does it match the names we have?
            if (($path->isDir() === true) && (in_array($name, $directories_to_remove, true)) && (substr_count($full_path, '/') === 4)) {
                // Is the directory the 4rd level deep?
                //
                // ie, vendor/somedude/somepackage/Tests
                //
                // to be safe and that make sure we don't impact
                // functionality we don't want to remove anything at a deeper
                // level than this.

                $size = $this->getDirectorySize($path);
                $total_removeable += $size;

                $directories_to_action[] = [
                    'name' => $name,
                    'path' => $path->getPathName(),
                    'size' => $size
                ];

                $output->writeln('path: '.$full_path);
                $output->writeln('name: '.$name);
                $output->writeln('is directory: '.$full_path);
                $output->writeln(' ');
            }
        }

        // Make sure we've got something to actually delete.
        if (count($directories_to_action) === 0) {
            $output->writeLn(' ');
            $output->writeLn('💾 Skrub found nothing to remove, sorry about that.');
            $output->writeLn(' ');
            exit();
        }

        // Inform the user of the disk space we can save.
        $output->writeLn(' ');
        $output->writeLn('💾 Skrub can save a total of: '.$this->bytesToHumanReadable($total_removeable).'.');
        $output->writeLn(' ');

        // Perform the actual deletion of files if we're asked to.
        if ($input->getOption('perform') === true) {
            foreach ($directories_to_action as $directory) {
                if ('\\' === DIRECTORY_SEPARATOR) {
                    shell_exec('rd /S /Q '.$directory['path']);
                } else {
                    shell_exec('rm -rf '.$directory['path']);
                }
                $output->writeLn('🗑  Deleting: '.$directory['path']. ' (saving '.$this->bytesToHumanReadable($directory['size']).')');

                if (!file_exists($directory['path']) === true) {
                    $output->writeln('✅ Successfully deleted.');
                } else {
                    $output->writeln('❌ Error deleting path: '.$directory['path']);
                }
            }
        } else {
            $output->writeLn('🔥 Run again with --perform to actually delete files.');
        }
        $output->writeLn(' ');
    }

    /**
     * Calculate the size of a directory recursively.
     *
     * @param  $dir
     * @return false|int
     */
    private function getDirectorySize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->getDirectorySize($each);
        }
        return $size;
    }

    /**
     * Display bytes in a human readable format.
     *
     * @param $bytes
     * @param int   $decimals
     *
     * @return bool|string
     */
    public function bytesToHumanReadable($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
