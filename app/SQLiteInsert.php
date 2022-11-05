<?php

namespace App;

use DateTimeImmutable;
use PDO;

/**
 * PHP SQLite Insert Demo
 */
class SQLiteInsert
{

    /**
     * PDO object
     * @var PDO
     */
    private $pdo;

    /**
     * Initialize the object with a specified PDO object
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertConvertedFileRecord($original_file_path, $converted_file_path)
    {
        $sql = 'INSERT INTO converted_files(original_file_path,converted_file_path,created_at) '
            . 'VALUES(:original_file_path,:converted_file_path,:created_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':original_file_path' => $original_file_path,
            ':converted_file_path' => $converted_file_path,
            ':created_at' => (new DateTimeImmutable())->format('d-m-Y H:i'),
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getLast10ConvertedFiles()
    {
        $sql = 'SELECT * FROM converted_files ORDER BY id DESC LIMIT 10';

        return $this->pdo->query($sql)->fetchAll();
    }
}