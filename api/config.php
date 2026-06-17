<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$host = '127.0.0.1';
$database = 'manajemen_dokumen_proyek';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal. Pastikan database sudah dibuat di phpMyAdmin.',
        'error' => $exception->getMessage(),
    ]);
    exit;
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function project_upload_dir(): string
{
    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';

    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    return $directory;
}
