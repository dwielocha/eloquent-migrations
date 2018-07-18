<?php

use Dwielocha\EloquentMigrations\Migration;

/**
 * Test migration
 */
class TestMigration extends Migration
{
    /**
     * Run migration
     */
    public function up()
    {
        echo "Test migration says: UP UP UP!\n";
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        echo "Test migration says: DOWN DOWN DOWN!\n";
    }
}