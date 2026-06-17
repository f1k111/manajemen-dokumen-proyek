<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['_method'] ?? $method;

if ($method === 'GET') {
    $projects = $pdo
        ->query('SELECT id, name, category, signature_data, created_at, updated_at FROM projects ORDER BY id DESC')
        ->fetchAll();

    $fileStatement = $pdo->prepare('SELECT id, original_name, file_path, mime_type, file_size FROM project_files WHERE project_id = ? ORDER BY id ASC');

    foreach ($projects as &$project) {
        $fileStatement->execute([$project['id']]);
        $project['files'] = $fileStatement->fetchAll();
    }

    json_response([
        'success' => true,
        'data' => $projects,
    ]);
}

if ($method === 'POST' && strtoupper((string) $action) === 'DELETE') {
    $id = (int) ($_POST['id'] ?? 0);

    if ($id < 1) {
        json_response(['success' => false, 'message' => 'ID proyek tidak valid.'], 422);
    }

    $statement = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $statement->execute([$id]);

    json_response([
        'success' => true,
        'message' => 'Data berhasil dihapus.',
    ]);
}

if ($method === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $signature = trim((string) ($_POST['signature_data'] ?? ''));

    if ($name === '' || $category === '' || $signature === '') {
        json_response([
            'success' => false,
            'message' => 'Nama proyek, kategori, dan tanda tangan wajib diisi.',
        ], 422);
    }

    $pdo->beginTransaction();

    try {
        if ($id > 0) {
            $statement = $pdo->prepare('UPDATE projects SET name = ?, category = ?, signature_data = ? WHERE id = ?');
            $statement->execute([$name, $category, $signature, $id]);
            $projectId = $id;
        } else {
            $statement = $pdo->prepare('INSERT INTO projects (name, category, signature_data) VALUES (?, ?, ?)');
            $statement->execute([$name, $category, $signature]);
            $projectId = (int) $pdo->lastInsertId();
        }

        save_uploaded_files($pdo, $projectId);

        $pdo->commit();

        json_response([
            'success' => true,
            'message' => $id > 0 ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.',
        ]);
    } catch (Throwable $exception) {
        $pdo->rollBack();
        json_response([
            'success' => false,
            'message' => 'Data gagal disimpan.',
            'error' => $exception->getMessage(),
        ], 500);
    }
}

json_response(['success' => false, 'message' => 'Metode request tidak didukung.'], 405);

function save_uploaded_files(PDO $pdo, int $projectId): void
{
    if (!isset($_FILES['files'])) {
        return;
    }

    $files = $_FILES['files'];
    $uploadDirectory = project_upload_dir();

    foreach ($files['name'] as $index => $originalName) {
        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            continue;
        }

        $extension = pathinfo((string) $originalName, PATHINFO_EXTENSION);
        $storedName = uniqid('project_', true) . ($extension ? ".{$extension}" : '');
        $targetPath = $uploadDirectory . DIRECTORY_SEPARATOR . $storedName;

        if (!move_uploaded_file($files['tmp_name'][$index], $targetPath)) {
            continue;
        }

        $relativePath = 'uploads/' . $storedName;
        $statement = $pdo->prepare(
            'INSERT INTO project_files (project_id, original_name, stored_name, file_path, mime_type, file_size)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $projectId,
            $originalName,
            $storedName,
            $relativePath,
            $files['type'][$index] ?? null,
            (int) ($files['size'][$index] ?? 0),
        ]);
    }
}
