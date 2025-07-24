<?php
require_once(__DIR__ . '/../config.php'); // Ajusta la ruta según tu configuración de Moodle.
global $DB, $USER;

// Comprueba si el usuario está logueado y si es administrador.
$is_admin = isloggedin() && is_siteadmin($USER);

// Consulta la información de contacto desde la tabla mdl_dev_informacion.
$info_contacto = $DB->get_record('dev_informacion', ['id' => 1]);

// Consulta las preguntas frecuentes desde la tabla mdl_dev_preguntas_frecuentes.
$preguntas_frecuentes = $DB->get_records('dev_preguntas_frecuentes');

// Directorio donde están los archivos de la carpeta uploads.
$uploads_dir = __DIR__ . '/uploads';
$archivos = [];

// Comprueba si el directorio de uploads existe y escanea los archivos.
if (is_dir($uploads_dir)) {
    $archivos = array_diff(scandir($uploads_dir), ['.', '..', '.htaccess']); // Excluye .htaccess
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mesa de Ayuda - AprendeTIC</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* ================== Variables de color (inspiradas en Moodle) ================== */
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

    /* ================== Reset & Base ================== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: var(--moodle-bg);
      color: var(--moodle-secondary);
      line-height: 1.5;
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

    /* Botón de administración, visible solo para administradores */
    <?php if ($is_admin): ?>
    .admin-link {
      display: inline-block;
    }
    <?php else: ?>
    .admin-link {
      display: none;
    }
    <?php endif; ?>

    .admin-link {
      text-decoration: none;
      background-color: var(--moodle-secondary);
      color: var(--moodle-white);
      padding: 0.5rem 1rem;
      border-radius: 4px;
      font-weight: bold;
      transition: background-color var(--moodle-transition);
    }

    .admin-link:hover {
      background-color: var(--moodle-hover);
    }

    /* ================== Contenido Principal ================== */
    .content {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: var(--moodle-white);
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .content h2 {
      color: var(--moodle-base);
      margin-bottom: 1rem;
      font-size: 1.4rem;
      border-left: 4px solid var(--moodle-primary);
      padding-left: 0.5rem;
    }

    .content p, .content li {
      margin-bottom: 0.5rem;
      color: var(--moodle-secondary);
    }

    .content ul {
      list-style: none;
      padding-left: 1rem;
    }

    .content ul li {
      margin-left: 1rem;
      position: relative;
    }

    .content ul li::before {
      content: '•';
      color: var(--moodle-primary);
      position: absolute;
      left: -1rem;
    }

    /* ================== Tabla Responsiva ================== */
    .table-responsive {
      width: 100%;
      overflow-x: auto;
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
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }

    @media (max-width: 768px) {
      .table thead tr th,
      .table tbody tr td {
        font-size: 0.9rem;
        white-space: normal;
        word-break: break-word;
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

    /* ================== Formularios ================== */
    .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      display: block;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ced4da;
      border-radius: var(--border-radius);
      font-size: 1rem;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: background-color var(--moodle-transition);
    }

    .btn-primary {
      background-color: var(--moodle-primary);
      color: var(--moodle-white);
    }

    .btn-primary:hover {
      background-color: var(--moodle-base-hover);
    }

    .btn-warning {
      background-color: var(--moodle-hover);
      color: var(--moodle-white);
    }

    /* ================== Footer ================== */
    footer {
      background-color: var(--moodle-base);
      color: var(--moodle-white);
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
      font-size: 0.9rem;
    }

    footer a {
      color: var(--moodle-white);
      text-decoration: underline;
    }

    footer a:hover {
      text-decoration: none;
    }

    /* ================== Animaciones ================== */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .content, header, nav, .table, form, footer {
      animation: fadeInUp 0.5s ease-out;
    }

    /* ================== Responsividad General ================== */
    @media (max-width: 992px) {
      header h1 {
        font-size: 1.75rem;
      }
      .content {
        padding: 1.5rem;
      }
      nav {
        padding: 0.75rem 1.5rem;
      }
      nav div a {
        margin: 0 0.5rem;
      }
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        align-items: center;
      }
      nav div a {
        margin: 0.5rem 0;
      }
      .content {
        padding: 1rem;
        margin: 1rem;
      }
      .table thead tr th, .table tbody tr td {
        font-size: 0.9rem;
        padding: 0.5rem;
      }
      .form-control {
        padding: 0.65rem;
      }
      .btn {
        width: 100%;
        padding: 0.75rem;
        margin-top: 0.5rem;
      }
    }

    @media (max-width: 480px) {
      header h1 {
        font-size: 1.5rem;
      }
      nav div a {
        font-size: 0.9rem;
      }
      .content .section h2 {
        font-size: 1.5rem;
      }
      .table thead tr th, .table tbody tr td {
        font-size: 0.8rem;
      }
      .form-control {
        font-size: 0.9rem;
      }
      .btn {
        font-size: 0.9rem;
      }
    }
    /* Estilos para la tabla */
table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 16px;
  text-align: left;
  background-color: #f9f9f9;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
}

/* Encabezado de la tabla */
table thead tr {
  background-color: #00468b; /* Azul oscuro */
  color: #fff;
  text-align: left;
  font-weight: bold;
}

/* Celdas del encabezado */
table th,
table td {
  padding: 12px 15px;
}

/* Filas alternadas */
table tbody tr:nth-child(even) {
  background-color: #f1f1f1;
}

/* Hover en filas */
table tbody tr:hover {
  background-color: #e4f0ff; /* Azul claro */
}

/* Enlaces de descarga */
table a {
  color: #00468b;
  text-decoration: none;
  font-weight: bold;
  border: 1px solid #00468b;
  padding: 5px 10px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

/* Hover en enlaces */
table a:hover {
  background-color: #00468b;
  color: #fff;
}

/* Bordes redondeados para las celdas */
table th:first-child,
table td:first-child {
  border-top-left-radius: 8px;
  border-bottom-left-radius: 8px;
}

table th:last-child,
table td:last-child {
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
}

/* ================== FAQ Cards ================== */
#faqs {
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 1.5rem;
}

#faqs h2 {
    text-align: center;
    color: var(--moodle-primary);
    margin-bottom: 2.5rem;
    font-size: 2.2rem;
    position: relative;
    padding-bottom: 0.8rem;
}

#faqs h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: var(--moodle-primary);
    border-radius: 2px;
}

.cart-container {
    background: #ffffff;
    border-radius: 12px;
    padding: 1.8rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.cart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    border-color: var(--moodle-primary);
}

#faqs h3 {
    color: var(--moodle-secondary);
    margin-bottom: 1rem;
    font-size: 1.25rem;
    font-weight: 600;
    position: relative;
    padding-left: 1.8rem;
}

#faqs h3::before {
    content: '?';
    position: absolute;
    left: 0;
    top: -2px;
    width: 24px;
    height: 24px;
    background: var(--moodle-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 700;
}

#faqs p {
    color: #555;
    line-height: 1.7;
    font-size: 0.95rem;
    margin-left: 1.8rem;
    padding-left: 1rem;
    border-left: 2px solid #eeeeee;
    transition: border-color 0.3s ease;
}

.cart-container:hover p {
    border-color: var(--moodle-primary);
}

#faqs > p {
    text-align: center;
    color: #666;
    font-style: italic;
    margin: 2rem 0;
}

/* ================== Responsive Design ================== */
@media (max-width: 992px) {
    #faqs {
        padding: 0 1rem;
    }
    
    .cart-container {
        padding: 1.5rem;
    }
    
    #faqs h3 {
        font-size: 1.15rem;
    }
}

@media (max-width: 768px) {
    #faqs h2 {
        font-size: 1.8rem;
        margin-bottom: 2rem;
    }
    
    #faqs h2::after {
        width: 80px;
    }
    
    #faqs h3 {
        padding-left: 1.5rem;
        margin-left: 0.5rem;
    }
    
    #faqs p {
        margin-left: 1.5rem;
        padding-left: 0.8rem;
    }
}

@media (max-width: 480px) {
    .cart-container {
        padding: 1.2rem;
        margin-bottom: 1rem;
    }
    
    #faqs h3 {
        font-size: 1.1rem;
        padding-left: 1.8rem;
    }
    
    #faqs h3::before {
        width: 20px;
        height: 20px;
        font-size: 0.8rem;
    }
    
    #faqs p {
        font-size: 0.9rem;
        margin-left: 1rem;
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
          <a href="#faqs">Preguntas Frecuentes</a>
          <a href="#uploads">Archivos</a>
          <a href="https://ibagueaprendetic.ibague.gov.co/my/courses.php">Ingresar a cursos </a>
          
         
      </div>
      <?php if ($is_admin): ?>
          <a href="./admin.php" class="admin-link">Editar Informacion</a>
      <?php endif; ?>
  </nav>

  <div class="content">
      <!-- Información de contacto -->
      <div id="info">
          <h2>Información de Contacto</h2>
          <?php if ($info_contacto): ?>
              <p><?php echo $info_contacto->descripcion; ?></p>
              <p><strong>Teléfono:</strong> <?php echo $info_contacto->telefono; ?></p>
              <p><strong>WhatsApp:</strong> <a href="https://wa.me/<?php echo str_replace('+', '', $info_contacto->whatsapp); ?>" target="_blank"><?php echo $info_contacto->whatsapp; ?></a></p>
              <p><strong>Correo:</strong> <a href="mailto:<?php echo $info_contacto->correo; ?>"><?php echo $info_contacto->correo; ?></a></p>
              <p><strong>Redes Sociales:</strong></p>
              <ul>
                  <li><a href="<?php echo $info_contacto->facebook; ?>" target="_blank">Facebook</a></li>
                  <li><a href="<?php echo $info_contacto->twitter; ?>" target="_blank">Twitter</a></li>
                  <li><a href="<?php echo $info_contacto->instagram; ?>" target="_blank">Instagram</a></li>
              </ul>
          <?php else: ?>
              <p>No hay información de contacto disponible.</p>
          <?php endif; ?>
      </div>

      <!-- Preguntas frecuentes -->
      <div id="faqs">
          <h2>Preguntas Frecuentes</h2>
          <?php if ($preguntas_frecuentes): ?>
              <?php foreach ($preguntas_frecuentes as $faq): ?>
                  <div class="cart-container">
                      <h3><?php echo $faq->pregunta; ?></h3>
                      <p><?php echo $faq->respuesta; ?></p>
                  </div>
              <?php endforeach; ?>
          <?php else: ?>
              <p>No hay preguntas frecuentes disponibles.</p>
          <?php endif; ?>
      </div>

      <!-- Lista de archivos -->
      <div id="uploads">
          <h2>Archivos</h2>
          <?php if ($archivos): ?>
              <table border="1">
                  <thead>
                      <tr>
                          <th>Nombre del Archivo</th>
                          <th>Tamaño</th>
                          <th>Opciones</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($archivos as $archivo): ?>
                          <?php $ruta_archivo = $uploads_dir . '/' . $archivo; ?>
                          <tr>
                              <td><?php echo $archivo; ?></td>
                              <td><?php echo round(filesize($ruta_archivo) / 1024, 2); ?> KB</td>
                              <td><a href="uploads/<?php echo $archivo; ?>" download>Descargar</a></td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          <?php else: ?>
              <p>No hay archivos disponibles.</p>
          <?php endif; ?>
      </div>
  </div>

  <footer>
      &copy; 2025 AprendeTIC - Mesa de Ayuda | <a href="#">Política de privacidad</a>
  </footer>
</body>
</html>
