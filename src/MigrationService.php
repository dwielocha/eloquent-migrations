<?php
namespace Dwielocha\EloquentMigrations;

use Dwielocha\EloquentMigrations\Exceptions\EmptyMigrationsPathException;
use Dwielocha\EloquentMigrations\Exceptions\MigrationClassNotUniqueException;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Migration service
 * 
 * @author Damian Wielocha <damian@wielocha.com>
 */
class MigrationService
{
    /**
     * Batch number
     * @var int
     */
    protected $batch = 0;

    /**
     * Name of table in DB where is stored list of installed migrations
     * 
     * @var string
     */
    protected $migrationsTableName = 'migrations';

    /**
     * Path for migration files
     * 
     * @var string
     */
    protected $migrationsPath = '';

    /**
     * Array of installed migrations
     * 
     * @var array
     */
    protected $installedMigrations = [];

    /**
     * Array of unique migration class names
     * 
     * @var array
     */
    protected $uniqueMigrationClasses = [];

    /**
     * Constructor
     * 
     * @param string $migrationsPath
     */
    public function __construct($migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * Return name of migrations table
     * 
     * @return string
     */
    public function getMigrationsTableName()
    {
        return $this->migrationsTableName;
    }

    /**
     * Change migrations table name
     * 
     * @param string $tableName
     * @return $this
     */
    public function setMigrationsTableName($tableName)
    {
        $this->migrationsTableName = $tableName;

        return $this;customers
    }

    /**
     * Create migrations table in DB
     * Function returns true if table was created, false if it already exists
     * 
     * @return bool
     */
    public function createMigrationsTable()
    {
        $schema = DB::schema();

        if ($schema->hasTable($this->migrationsTableName)) {
            return false;
        }

        $schema->create(
            $this->migrationsTableName, 
            function ($table) {
                $table->string('migration', 255);
                $table->integer('batch')->unsigned();
                $table->timestamp('created_at');
            }
        );

        return true;
    }

    /**
     * Install new migrations
     * 
     * @return array array('errors' => [], 'installed' => [])
     * @throws MigrationClassNotUniqueException
     */
    public function installNewMigrations()
    {
        $this->loadInstacustomers

        $installed = [];customers
        $errors = [];
        $now = date('Y-m-d H:i:s');
        $db = DB::connection();

        $migrationsTable = DB::table($this->migrationsTableName);
        $files = $this->getMigrationFiles($this->migrationsPath);

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            list(, $class) = explode('__', $filename);

            // Skip already installed migrations
            if (in_array($filename, $this->installedMigrations)) {
                continue;
            }

            // Make sure that migration class is unique
            if (!$this->isMigrationClassUnique($class)) {
                throw new MigrationClassNotUniqueException($class, $filename);
            }

            require_once $file;

            $db->beginTransaction();
            try {
                // launch migration
                $obj = new $class();
                $obj->up();

                // save migration info to db
                $migrationsTable->insert([
                    'migration' => $filename,
                    'batch' => $this->batch,
                    'created_at' => $now,
                ]);

                $db->commit();
                $installed[] = ['file' => $filename];
            }
            catch (Exception $ex) 
            {
                $db->rollBack();
                $errors[] = [
                    'file' => $file,
                    'message' => $ex->getMessage()
                ];
            }
        }

        // prepare result data
        $result = [
            'installed' => $installed,
            'errors' => $errors
        ];

        return $result;
    }

    /**
     * Fetch already installed migrations
     */
    protected function loadInstalledMigrations()
    {
        $batch = 0;
        $migrationsTable = DB::table($this->migrationsTableName);
        $migrated = $migrationsTable
            ->orderBy('batch', 'DESC')
            ->get();

        foreach ($migrated as $record) {
            $this->installedMigrations[] = $record->migration;

            if ($batch === 0) {
                $batch = $record->batch;
            }
        }

        // set new batch number
        $batch++;
        $this->batch = $batch;
    }

    /**
     * Return migration files
     * 
     * @param string $path
     * @return array
     * @throws EmptyMigrationsPathException
     */
    protected function getMigrationFiles($path)
    {
        if (empty($path)) {
            throw new EmptyMigrationsPathException;
        }

        $files = glob($path);
        asort($files);

        return $files;
    }

    /**
     * Check if migration class is unique
     * 
     * @param string $class
     * @return bool
     */
    protected function isMigrationClassUnique($class)
    {
        if (in_array($class, $this->uniqueMigrationClasses)) {
            return false;
        }

        $this->uniqueMigrationClasses[] = $class;

        return true;
    }
}