<?php 
use Dwielocha\EloquentMigrations\MigrationService;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;

/**
 * Migration service test
 * 
 * @author Damian Wielocha <damian@wielocha.com>
 */
class MigrationServiceTest extends TestCase
{
    /**
     * Check if migrations table can be properly created
     */
    public function testCanCreateMigrationsTable()
    {
        $this->clearDb();

        $schema = DB::schema();
        $service = new MigrationService();
        $tableName = $service->getMigrationsTableName();

        // Extra checking to be sure migrations table does not exist
        $service->createMigrationsTable();

        $this->assertTrue($schema->hasTable($tableName));
    }

    /**
     * Check if migrations table name can be switched
     */
    public function testIfMigrationsTableNameCanBeChanged()
    {
        $service = new MigrationService();
        $currentTableName = $service->getMigrationsTableName();
        $newTableName = 'new_migrations_'.date('YmdHis');

        $service->setMigrationsTableName($newTableName);
        $this->assertEquals($newTableName, $service->getMigrationsTableName());
    }

    /**
     * Clear DB before tests
     * @TODO this should be done by importing .sql file
     */
    protected function clearDb()
    {   
        $schema = DB::schema();
        $service = new MigrationService();

        $tableName = $service->getMigrationsTableName();
        $schema->dropIfExists($tableName);
    }
}
