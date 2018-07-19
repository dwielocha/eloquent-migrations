<?php
namespace Dwielocha\EloquentMigrations\Commands;

use Dwielocha\EloquentMigrations\MigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * CLI: migrate command
 * 
 * @author Damian Wielocha <damian@wielocha.com>
 */
class MigrateCommand extends Command
{
    /**
     * Command configuration
     * 
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('migrate')
            ->setDescription('Installs new migrations');
    }

    /**
     * Execute command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Eloquent Migrations - Install new migrations');
        $path = env('ELOQUENT_MIGRATIONS_PATH', '');

        $service = new MigrationService($path);
        $service->createMigrationsTable();

        $result = $service->installNewMigrations();

        if (count($result['installed'])) {
            $io->table(
                ['Installed migrations'], 
                $result['installed']
            );
        } 
        if (count($result['errors'])) {
            $io->error('There were some errors while installing new migrations:');
            $i = 1;
            foreach ($result['errors'] as $error) {
                $io->text("{$i}. Error in {$error['file']}");
                $io->text("{$error['message']}");
                $io->text('');
                $i++;
            }
        }

        if (empty($result['installed']) && empty($result['errors'])) {
            $io->text('Nothing new to install.');
        }

        // Empty line for better readability
        $io->text('');
    }
}