<?php
header('Content-Type: application/json');

// Datos que se enviarán como respuesta desde la API.
$data = [
    'helpLink' => 'https://ibagueaprendetic.ibague.gov.co/mesa_de_ayuda/index.php', // URL del enlace de ayuda.
    'iconPath' => 'https://ibagueaprendetic.ibague.gov.co/mesa_de_ayuda/img/help.svg', // Ruta completa del ícono.
    'helpText' => '¿Mesa de ayuda aprendetic 2025?' // Texto del enlace.
];

// Convertir los datos a formato JSON y enviarlos al cliente.
echo json_encode($data);
