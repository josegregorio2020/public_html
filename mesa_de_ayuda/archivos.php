<?php
// Verificar si la clase ya está declarada
if (!class_exists('FileHandler')) {

class FileHandler
{
    private $uploadDirectory;

    public function __construct($uploadDirectory = './uploads/')
    {
        $this->uploadDirectory = rtrim($uploadDirectory, '/') . '/';
        $this->ensureUploadDirectoryExists();
    }

    private function ensureUploadDirectoryExists()
    {
        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0777, true);
        }
    }

    public function uploadFile($file)
    {
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $fileName = basename($file['name']);
            $destination = $this->uploadDirectory . $fileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                return ['success' => true, 'message' => 'Archivo subido exitosamente.'];
            } else {
                return ['success' => false, 'message' => 'Error al mover el archivo al directorio de destino.'];
            }
        } else {
            return ['success' => false, 'message' => 'Error al subir el archivo.'];
        }
    }

    public function listFiles()
    {
        return array_values(array_diff(scandir($this->uploadDirectory), ['.', '..']));
    }

    public function deleteFile($fileName)
    {
        $filePath = $this->uploadDirectory . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
            return ['success' => true, 'message' => 'Archivo eliminado exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'El archivo no existe.'];
        }
    }
}

}

// Procesar las solicitudes
$fileHandler = new FileHandler('./uploads');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $result = $fileHandler->uploadFile($_FILES['file']);
        $message = $result['message'];
    }

    if (isset($_POST['deleteFile'])) {
        $result = $fileHandler->deleteFile($_POST['deleteFile']);
        $message = $result['message'];
    }
}

$files = $fileHandler->listFiles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Gestión de Archivos</h1>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Subir Archivo</h5>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file" class="form-label">Selecciona un archivo:</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Subir</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Archivos Subidos</h5>
            <?php if (!empty($files)): ?>
                <ul class="list-group">
                    <?php foreach ($files as $file): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($file) ?>
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="deleteFile" value="<?= htmlspecialchars($file) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No hay archivos subidos.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>