<?php

namespace Config;

use PDO;
use PDOException;

class Productos {
    private string $host;
    private string $dataBase;
    private string $user;
    private string $password;
    private string $charset;

    public function __construct(string $host, string $dataBase,string $user, string $password, string $charset)
    {
        $this->host= $host;
        $this->dataBase= $dataBase;
        $this->user = $user;
        $this->password = $password;
        $this->charset = $charset;

    }

    public function conexion()
    {
        $dsn= "mysql:host = $this->host; dbname= $this->dataBase;charset=$this->charset ";

    try {
        $pdo= new PDO(
            $dsn, 
            $this->user,
            $this->password
        );
        return $pdo;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

}
