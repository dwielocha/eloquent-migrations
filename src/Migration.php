<?php
namespace Dwielocha\EloquentMigrations;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Migration template class
 */
abstract class Migration
{
    /**
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Constructor
     */
    public function __construct()
    {
        // We've not injecting this as argument, due to requirements of project:
        // eloquent is globaly used
        $this->schema = DB::schema();
    }

    /**
     * Commit migration
     */
    abstract public function up();
    
    /**
     * Rollback migration
     */
    abstract public function down();
}