<?php
namespace Dwielocha\EloquentMigrations\Exceptions;

/**
 * Exception: migration class is not unique
 * 
 * @author Damian Wielocha <damian@wielocha.com>
 */
class MigrationClassNotUniqueException extends \Exception
{
    /**
     * Constructor
     * 
     * @param string $class
     * @param string $filename
     */
    public function __construct($class, $filename)
    {
        $message = "The migration class `{$class}` already exists - please rename class in file `{$filename}`.";
        parent::__construct($message);
    }
}