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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CleanCommand
 *
 * @package Grav\Console\Cli
 */
class LsCommand extends ConsoleCommand
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
            ->setName('ls')
            ->addArgument(
                'slug',
                InputArgument::OPTIONAL,
                'The page slug or unique ID',
                ''
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Limit the type of views',
                null
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit the list of views',
                10
            )
            ->addOption(
                'sort',
                's',
                InputOption::VALUE_OPTIONAL,
                'Sort the list of views (desc / asc)',
                'desc'
            )
            ->setDescription('List the views count')
            ->setHelp('The <info>list</info> command displays the views count')
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
        $limit = $this->input->getOption('limit');
        $sort = $this->input->getOption('sort');
        $type = $this->input->getOption('type');

        $views = $grav['views'];

        $table = new Table($this->output);
        $table->setStyle('box');
        $table->setHeaders(['Slug', 'Type', 'Views Count']);
        $rows = [];

        if ($slug) {
            $rows[] = [$slug, $type, $views->get($slug, $type)];
        } else {
            $total = $views->getAll($type, $limit, $sort);
            foreach ($total as $view) {
                $rows[] = [$view['id'], $view['type'], $view['count']];
            }
        }

        $io->title('Page Views List');
        $table->setRows($rows);
        $table->render();
        $io->newLine();
    }
}
