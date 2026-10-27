<?php
declare(strict_types=1);

/**
 * Conexión del ejercicio 06 (MySQL / MariaDB de XAMPP).
 * En XAMPP, por defecto: host 127.0.0.1, puerto 3306, usuario root, contraseña vacía.
 * Puedes modificar los valores por defecto o usar variables de entorno G5_DB_*.
 */
function g5_db_options(): array {
    $port = (int) (getenv('G5_DB_PORT') ?: '3306');
    return [
        'host' => getenv('G5_DB_HOST') ?: '127.0.0.1',
        'port' => $port > 0 ? $port : 3306,
        'name' => getenv('G5_DB_NAME') ?: 'pwatarea3_grupo5',
        'user' => getenv('G5_DB_USER') ?: 'root',
        'pass' => getenv('G5_DB_PASS') !== false ? getenv('G5_DB_PASS') : '',
    ];
}

function g5_is_local(): bool {
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host);
    return in_array($remote, ['127.0.0.1', '::1'], true)
        && in_array($host, ['127.0.0.1', 'localhost', '[::1]'], true);
}

function g5_demo_accounts(): array {
    return [
        ['nombre' => 'Jorge Toapanta', 'email' => 'jorge.toapanta@grupo5.local'],
        ['nombre' => 'Jhoely Dávila', 'email' => 'jhoely.davila@grupo5.local'],
        ['nombre' => 'Wilmer Adriano', 'email' => 'wilmer.adriano@grupo5.local'],
        ['nombre' => 'Jhonatan Chávez', 'email' => 'jhonatan.chavez@grupo5.local'],
        ['nombre' => 'Wilmer López', 'email' => 'wilmer.lopez@grupo5.local'],
    ];
}

function db(bool $withDatabase = true): PDO {
    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('PHP no tiene habilitada la extensión pdo_mysql.');
    }
    $o = g5_db_options();
    $dsn = 'mysql:host='.$o['host'].';port='.$o['port'].';'.($withDatabase ? 'dbname='.$o['name'].';' : '').'charset=utf8mb4';
    return new PDO($dsn, $o['user'], $o['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 3,
    ]);
}

/**
 * Crea la base y los cinco usuarios de prueba, únicamente desde localhost.
 * Se puede ejecutar más de una vez: no sobrescribe contraseñas existentes.
 * Si MySQL no está encendido, lanza excepción. El login dispone de modo demo local.
 */
function g5_install_mysql(): PDO {
    if (!g5_is_local()) {
        throw new RuntimeException('La instalación automática solo está permitida en localhost.');
    }
    $o = g5_db_options();
    if (!preg_match('/^[a-zA-Z0-9_]+$/D', $o['name'])) {
        throw new RuntimeException('Nombre de la base de datos inválido.');
    }
    $server = db(false);
    $dbName = '`'.$o['name'].'`';
    $server->exec('CREATE DATABASE IF NOT EXISTS '.$dbName.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo = db();
    $pdo->exec('CREATE TABLE IF NOT EXISTS usuarios (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB');
    $stmt = $pdo->prepare('INSERT IGNORE INTO usuarios (nombre,email,password_hash) VALUES (:nombre,:email,:hash)');
    foreach (g5_demo_accounts() as $account) {
        $stmt->execute([
            'nombre' => $account['nombre'],
            'email' => $account['email'],
            'hash' => password_hash('Grupo5_2026!', PASSWORD_DEFAULT),
        ]);
    }
    return $pdo;
}

/** Intenta instalar automáticamente al iniciar sesión desde localhost. */
function g5_login_connection(): PDO {
    return g5_is_local() ? g5_install_mysql() : db();
}

/** Acceso de emergencia para la demostración en el propio computador. Nunca en hosting. */
function g5_demo_credentials(string $email, string $password): ?array {
    if (!g5_is_local() || !hash_equals('Grupo5_2026!', $password)) {
        return null;
    }
    foreach (g5_demo_accounts() as $i => $account) {
        if (hash_equals($account['email'], $email)) {
            return ['id' => $i + 1, 'nombre' => $account['nombre'], 'email' => $account['email']];
        }
    }
    return null;
}
