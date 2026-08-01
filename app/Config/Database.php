<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private string $host = "localhost";
    private string $database = "ECOMMERCE";
    private string $user = "php";
    private string $password = "";
    private string $charset;

    public function __construct(string $host, string $database,string $user, string $password, string $charset)
    {
        $this->host= $host;
        $this->database= $database;
        $this->user = $user;
        $this->password = $password;
        $this->charset = $charset;

    }

    public function devolverConexion(): PDO
    {
        $dsn= "mysql:host=$this->host;dbname=$this->database;charset=$this->charset";

    try {
        $pdo= new PDO(
            $dsn, 
            $this->user,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
            ]

        );
        return $pdo;

    } catch (PDOException $e) {
        die ("Error." . $e->getMessage());
    }
}

}
