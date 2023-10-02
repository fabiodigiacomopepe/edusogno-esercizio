<?php
// Definisco le credenziali
define('servername', '127.0.0.1');
define('username', 'root');
define('password', 'root');
define('name', 'db_edusogno');
define('port', 3306);

// Mi connetto al DB
$connect = new mysqli(servername, username, password, name, port);

// Controllo la connessione
if ($connect && $connect->connect_error) {
    die($connect->connect_error);
}
