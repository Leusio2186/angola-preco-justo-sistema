<?php

namespace App\Core;

use PDO;
use App\Core\QueryBuilder;

class DB
{
    private static ?PDO $connection = null;

    /**
     * Obter a conexão PDO.
     * A conexão é criada apenas na primeira chamada.
     */
    public static function connection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = new PDO(
                'mysql:host=' .  DB_HOST .
                    ';dbname=' . DB_NAME .
                    ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }

    /**
     * Iniciar um Query Builder para uma tabela.
     */
    public static function table(string $table): QueryBuilder
    {
        return new QueryBuilder(self::connection(), $table);
    }
}
