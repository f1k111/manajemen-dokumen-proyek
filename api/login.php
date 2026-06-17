<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Metode request tidak didukung.'], 405);
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    json_response(['success' => false, 'message' => 'Email dan password wajib diisi.'], 422);
}

$statement = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
$statement->execute([$email]);
$user = $statement->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    json_response(['success' => false, 'message' => 'Email atau password tidak sesuai dengan database.'], 401);
}

json_response([
    'success' => true,
    'message' => 'Login berhasil.',
    'user' => [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ],
]);
