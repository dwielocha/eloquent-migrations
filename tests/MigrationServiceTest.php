<?php 
use Dwielocha\EloquentMigrations\MigrationService;
use Dwielocha\EloquentMigrations\Exceptions\EmptyMigrationsPathException;
use Dwielocha\EloquentMigrations\Exceptions\MigrationClassNotUniqueException;
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
        $schema = DB::schema();
        $service = new MigrationService(getenv('ELOQUENT_MIGRATIONS_PATH'));
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
        $service = new MigrationService(getenv('ELOQUENT_MIGRATIONS_PATH'));
        $currentTableName = $service->getMigrationsTableName();
        $newTableName = 'new_migrations_'.date('YmdHis');

        $service->setMigrationsTableName($newTableName);
        $this->assertEquals($newTableName, $service->getMigrationsTableName());
    }

    /**
     * Check if error is throwed when migrations path is not configured
     */
    public function testMigrationsPathNotConfiguredError()
    {
        try {
            // clear env to check if error will be triggered
            $service = new MigrationService('');
            $service->createMigrationsTable();
            $service->installNewMigrations();
            $this->assertTrue(false);
        } catch (EmptyMigrationsPathException $ex) {
            $this->assertTrue(true);
        }
    }

    /**
     * Check if running migration works
     */
    public function testRunningMigrations()
    {
        $service = new MigrationService(getenv('ELOQUENT_MIGRATIONS_PATH'));
        $service->createMigrationsTable();
        $result = $service->installNewMigrations();

        // We are checking result structure too
        $this->assertTrue(isset($result['installed']));
        $this->assertTrue(isset($result['errors']));
        $this->assertTrue(count($result['installed']) == 1);
        $this->assertTrue(count($result['errors']) == 0);
    }

    /**
     * Check if service triggers exception on duplicated migration classes
     */
    public function testDuplicatedMigrationClassError()
    {
        try {
            $service = new MigrationService('src/*TestMigration.php');
            $service->createMigrationsTable();
            $service->installNewMigrations();
            $this->assertTrue(false);
        } catch (MigrationClassNotUniqueException $ex) {
            $this->assertTrue(true);
        }
    }


    /**
     * Clear DB before running the test
     * @TODO this should be done by importing .sql file
     */
    protected function setUp()
    {   
        $schema = DB::schema();
        $service = new MigrationService(getenv('ELOQUENT_MIGRATIONS_PATH'));

        $tableName = $service->getMigrationsTableName();
        $schema->dropIfExists($tableName);
    }
}
