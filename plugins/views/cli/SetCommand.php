<?php
/**
 * @package    Grav\Plugin\Views
 *
 * @copyright  Copyright (C) 2014 - 2017 Trilby Media, LLC. All rights reserved.
 * @license    MIT License; see LICENSE file for details.
 */
namespace Grav\Plugin\Console;

use Grav\Console\ConsoleCommand;
use Grav\Common\Grav;
use Grav\Plugin\Database\Database;
use Grav\Plugin\Views\Views;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CleanCommand
 *
 * @package Grav\Console\Cli
 */
class SetCommand extends ConsoleCommand
{
    /** @var array */
    protected $options = [];

    /** @var Views */
    protected $views;

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('set')
            ->addArgument(
                'slug',
                InputArgument::REQUIRED,
                'The page slug or unique ID'
            )
            ->addArgument(
                'count',
                InputArgument::REQUIRED,
                'The views count'
            )
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'The view type',
                'pages'

            )
            ->setHelp('Set the views count for a anything, although it tracks page views by default')
        ;
    }

    /**
     * @return int|null|void
     */
    protected function serve()
    {
        include __DIR__ . '/../vendor/autoload.php';

        $grav = Grav::instance();
        $io = new SymfonyStyle($this->input, $this->output);

        // Initialize Plugins
        $grav->fireEvent('onPluginsInitialized');

        $slug = $this->input->getArgument('slug');
        $count = $this->input->getArgument('count');
        $type = $this->input->getArgument('type');

        $views = $grav['views'];

        $views->set($slug, $type, $count);

        $io->title('Set Page View Count');
        $io->text('<green>'. $slug . '</green> ' . $type . ' view updated to <cyan>' . $count . '</cyan>');
        $io->newLine();
    }
}
