<?php

namespace App;

use PDO;

/**
 * SQLite connection
 */
class SQLiteConnection
{
    private $pdo;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return PDO
     */
    public function connect(): PDO
    {
        $this->pdo = new PDO('sqlite:' . Config::PATH_TO_SQLITE_FILE);
        $this->createTables();
        return $this->pdo;
    }

    public function createTables(): void
    {
        $command = 'create table if not exists converted_files(
    id                  INTEGER constraint converted_files_pk primary key autoincrement,
    original_file_path  TEXT                  not null,
    converted_file_path TEXT                  not null,
    created_at          any default TIMESTAMP not null)';
        // execute the sql commands to create new tables
        $this->pdo->exec($command);
    }
}