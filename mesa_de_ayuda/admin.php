<?php
require_once(__DIR__ . '/../config.php'); // Ajusta la ruta según tu configuración de Moodle.

global $DB, $USER;

// Verifica si el usuario está logueado.
require_login();

// Comprueba si el usuario tiene el rol de administrador.
if (!is_siteadmin($USER)) {
    die('Acceso denegado: solo administradores pueden gestionar esta sección.');
}

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

// Acción seleccionada
$action = isset($_GET['action']) ? $_GET['action'] : null;
$table = isset($_GET['table']) ? $_GET['table'] : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;


// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($table === 'informacion') {
        $descripcion = $_POST['descripcion'];
        $telefono = $_POST['telefono'];
        $whatsapp = $_POST['whatsapp'];
        $correo = $_POST['correo'];
        $facebook = $_POST['facebook'];
        $twitter = $_POST['twitter'];
        $instagram = $_POST['instagram'];

        if (!empty($_POST['id'])) {
            // Actualizar información
            $DB->update_record('dev_informacion', [
                'id' => $_POST['id'],
                'descripcion' => $descripcion,
                'telefono' => $telefono,
                'whatsapp' => $whatsapp,
                'correo' => $correo,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'instagram' => $instagram,
            ]);
        } else {
            // Insertar nueva información
            $DB->insert_record('dev_informacion', [
                'descripcion' => $descripcion,
                'telefono' => $telefono,
                'whatsapp' => $whatsapp,
                'correo' => $correo,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'instagram' => $instagram,
            ]);
        }
    } elseif ($table === 'preguntas_frecuentes') {
        $pregunta = $_POST['pregunta'];
        $respuesta = $_POST['respuesta'];

        if (!empty($_POST['id'])) {
            // Actualizar pregunta frecuente
            $DB->update_record('dev_preguntas_frecuentes', [
                'id' => $_POST['id'],
                'pregunta' => $pregunta,
                'respuesta' => $respuesta,
            ]);
        } else {
            // Insertar nueva pregunta frecuente
            $DB->insert_record('dev_preguntas_frecuentes', [
                'pregunta' => $pregunta,
                'respuesta' => $respuesta,
            ]);
        }
    }
    header('Location: admin.php?table=' . $table);
    exit();
}

// Eliminar registros
if ($action === 'delete' && $id) {
    if ($table === 'informacion') {
        $DB->delete_records('dev_informacion', ['id' => $id]);
    } elseif ($table === 'preguntas_frecuentes') {
        $DB->delete_records('dev_preguntas_frecuentes', ['id' => $id]);
    }
    header('Location: admin.php?table=' . $table);
    exit();
}

// Precargar datos para edición
$record = null;
if ($action === 'edit' && $id) {
    if ($table === 'informacion') {
        $record = $DB->get_record('dev_informacion', ['id' => $id]);
    } elseif ($table === 'preguntas_frecuentes') {
        $record = $DB->get_record('dev_preguntas_frecuentes', ['id' => $id]);
    }
}

// Obtención de datos
$informacion = $DB->get_records('dev_informacion');
$preguntas_frecuentes = $DB->get_records('dev_preguntas_frecuentes');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>

:root {
      --moodle-primary: #0073AA;   /* Naranja principal de Moodle */
      --moodle-secondary: #2a2b2c; /* Texto oscuro */
      --moodle-bg: #f2f2f2;        /* Fondo claro */
      --moodle-hover: rgb(19, 62, 204); /* Hover anaranjado (ajustado) */
      --moodle-hero-bg: #fff8e6;   /* Fondo suave para hero */
      --moodle-white: #fff;
      --moodle-base: #3A78F0;
      --moodle-base-hover: rgb(58, 123, 255);
      --moodle-transition: 0.3s ease;
      --header-personalizado: #3A78F0;
    }

        /* ================== Header ================== */
    header {
      background-color: var(--header-personalizado);
      color: var(--moodle-white);
      padding: 1.5rem 2rem;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    header h1 {
      font-size: 1.8rem;
      font-weight: bold;
    }

/* ================== Navigation ================== */
nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: var(--moodle-white);
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      border-top: 1px solid #e0e0e0;
      border-bottom: 2px solid var(--moodle-primary);
    }

    nav div a {
      margin-right: 1rem;
      text-decoration: none;
      color: var(--moodle-secondary);
      font-weight: 500;
      transition: color var(--moodle-transition);
    }

    nav div a:hover {
      color: var(--moodle-base-hover);
    }

    /* ================== Responsive Navigation ================== */
@media (max-width: 768px) {
  nav {
    padding: 1rem;
    flex-wrap: wrap;
  }
  
  nav div {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.5rem;
  }
  
  nav div a {
    margin-right: 0;
    padding: 0.5rem;
    text-align: center;
    border-bottom: 1px solid #eeeeee;
  }
  
  nav div a:last-child {
    border-bottom: none;
  }
}

@media (max-width: 480px) {
  nav div a {
    font-size: 0.9rem;
    padding: 0.75rem;
  }
}

 /*tablas */

.table-responsive {
  width: 100%;
  overflow-x: auto; /* Permite scroll horizontal si es necesario */
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
}

.table thead tr th,
.table tbody tr td {
  vertical-align: top;
  border: 1px solid #dee2e6;
  padding: 0.75rem;
  /* En pantallas grandes, el contenido se mantiene en una línea */
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

/* Para pantallas medianas y pequeñas, permitimos que el texto se envuelva */
@media (max-width: 768px) {
  .table thead tr th,
  .table tbody tr td {
    font-size: 0.9rem;
    white-space: normal; /* Permite envolver el texto */
    word-break: break-word; /* Rompe palabras largas si es necesario */
  }
}

@media (max-width: 480px) {
  .table thead tr th,
  .table tbody tr td {
    font-size: 0.8rem;
    white-space: normal;
    word-break: break-word;
  }
}


    </style>
</head>

<body>
<header>
      <h1>Mesa de Ayuda - AprendeTIC</h1>
  </header>
<nav>
      <div>
          <a href="/">Inicio Aprendetic</a>
          <a href="/mesa_de_ayuda/index.php#faqs">Preguntas Frecuentes</a>
          <a href="/mesa_de_ayuda/index.php#uploads">Archivos</a>
          <a href="/my/courses.php">Ingresar a cursos </a>
          
         
      </div>
      
  </nav>

<?php require_once(__DIR__ . '/archivos.php');?>
<div class="container mt-5">
<h1 class="text-center">Administración de Contenido</h1>
    <nav class="nav nav-pills nav-fill mt-3">
        <a class="nav-link <?= $table === 'informacion' ? 'active' : '' ?>" href="?table=informacion">Información</a>
        <a class="nav-link <?= $table === 'preguntas_frecuentes' ? 'active' : '' ?>" href="?table=preguntas_frecuentes">Preguntas Frecuentes</a>
        
    </nav>



    <?php if ($table === 'informacion' || !$table): ?>
        <h2 class="mt-4">Información Básica</h2>
        
        <!-- Envolvemos la tabla en .table-responsive -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Teléfono</th>
                        <th>WhatsApp</th>
                        <th>Correo</th>
                        <th>Facebook</th>
                        <th>Twitter</th>
                        <th>Instagram</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($informacion as $info): ?>
                        <tr>
                            <td><?= $info->id ?></td>
                            <td><?= $info->descripcion ?></td>
                            <td><?= $info->telefono ?></td>
                            <td><?= $info->whatsapp ?></td>
                            <td><?= $info->correo ?></td>
                            <td><?= $info->facebook ?></td>
                            <td><?= $info->twitter ?></td>
                            <td><?= $info->instagram ?></td>
                            <td>
                                <a href="?action=edit&table=informacion&id=<?= $info->id ?>" class="btn btn-sm btn-warning">Editar</a>
                    <!--  <a href="?action=delete&table=informacion&id=<?= $info->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div> <!-- Fin de .table-responsive -->

        <h3 class="mt-4"><?= isset($_GET['id']) ? 'Editar Información' : 'Agregar Información' ?></h3>
        <form action="admin.php?table=informacion" method="POST">
            <input type="hidden" name="id" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion" rows="3" required><?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'descripcion', ['id' => $_GET['id']]) : '' ?></textarea>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" name="telefono" id="telefono" value="<?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'telefono', ['id' => $_GET['id']]) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="whatsapp" class="form-label">WhatsApp</label>
                <input type="text" class="form-control" name="whatsapp" id="whatsapp" value="<?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'whatsapp', ['id' => $_GET['id']]) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo" id="correo" value="<?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'correo', ['id' => $_GET['id']]) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="facebook" class="form-label">Facebook</label>
                <input type="text" class="form-control" name="facebook" id="facebook" value="<?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'facebook', ['id' => $_GET['id']]) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="twitter" class="form-label">Twitter</label>
                <input type="text" class="form-control" name="twitter" id="twitter" value="<?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'twitter', ['id' => $_GET['id']]) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="instagram" class="form-label">Instagram</label>
                <input type="text" class="form-control" name="instagram" id="instagram" value="<?= isset($_GET['id']) ? $DB->get_field('dev_informacion', 'instagram', ['id' => $_GET['id']]) : '' ?>">
            </div>
            <button type="submit" class="btn btn-<?= isset($_GET['id']) ? 'warning' : 'primary' ?>">
                <?= isset($_GET['id']) ? 'Actualizar' : 'Guardar' ?>
            </button>
        </form>

    <?php elseif ($table === 'preguntas_frecuentes'): ?>
        <h2 class="mt-4">Preguntas Frecuentes</h2>
        
        <!-- Envolvemos la tabla en .table-responsive -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pregunta</th>
                        <th>Respuesta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preguntas_frecuentes as $faq): ?>
                        <tr>
                            <td><?= $faq->id ?></td>
                            <td><?= $faq->pregunta ?></td>
                            <td><?= $faq->respuesta ?></td>
                            <td>
                                <a href="?action=edit&table=preguntas_frecuentes&id=<?= $faq->id ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="?action=delete&table=preguntas_frecuentes&id=<?= $faq->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div> <!-- Fin de .table-responsive -->

        <h3 class="mt-4">Agregar/Editar Pregunta Frecuente</h3>
        <form action="admin.php?table=preguntas_frecuentes" method="POST">
            <input type="hidden" name="id" value="<?= $record->id ?? '' ?>">
            <div class="mb-3">
                <label for="pregunta" class="form-label">Pregunta</label>
                <input type="text" class="form-control" name="pregunta" id="pregunta" value="<?= $record->pregunta ?? '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="respuesta" class="form-label">Respuesta</label>
                <textarea class="form-control" name="respuesta" id="respuesta" rows="3" required><?= $record->respuesta ?? '' ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    <?php endif; ?>
</div>

</body>

</html>