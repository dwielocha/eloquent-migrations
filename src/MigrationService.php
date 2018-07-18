<?php
namespace Dwielocha\EloquentMigrations;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Migration service
 * 
 * @author Damian Wielocha <damian@wielocha.com>
 */
class MigrationService
{
    /**
     * Name of table in DB where is stored list of installed migrations
     * 
     * @var string
     */
    protected $migrationsTableName = 'migrations';


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

        return $this;
    }
}