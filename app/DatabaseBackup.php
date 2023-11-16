<?php

namespace App;

use Ifsnop\Mysqldump as IMysqldump;
use Illuminate\Support\Facades\Log;

class DatabaseBackup
{
    private ?IMysqldump\Mysqldump $myDump = null;

    public function __construct()
    {
        $host = config('database.connections.mysql.host');
        $user = config('database.connections.mysql.username');
        $pwd = config('database.connections.mysql.password');
        $db = config('database.connections.mysql.database');
        $dsn = "mysql:host=$host;dbname=$db";

        $this->myDump = new IMysqldump\Mysqldump($dsn, $user, $pwd);
    }

    public function execute()
    {
        try {
            if ($this->myDump) {
                Log::info("Creating backup for database");
                $this->myDump->start(storage_path('backup/db-backup.sql'));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
//            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }
}
