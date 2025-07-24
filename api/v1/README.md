# API de Programadores - AprendeTIC

## Descripción
Esta API proporciona funcionalidades para gestionar programadores y usuarios en la plataforma AprendeTIC. Se incluyen rutas protegidas mediante token y sesión de Moodle.

## Base URL
```
https://ibagueaprendetic.ibague.gov.co/api/v1/
```

## Endpoints

### 1. Listar Usuarios con Paginación
**GET** `https://ibagueaprendetic.ibague.gov.co/api/v1/GetUsers.php?page=3`

**Parámetros:**
- `page` (opcional, int): Número de página.
- `limit` (opcional, int): Cantidad de registros por página.

**Respuesta:**
```json
{
  "status": "success",
  "total": 100,
  "page": 1,
  "limit": 10,
  "users": [ {...} ]
}
```

---
### 2. Listar Usuarios de Moodle
**GET** `/https://ibagueaprendetic.ibague.gov.co/api/v1/GetUsers.php`

**Respuesta:**
```json
{
  "status": "success",
  "users": [ {...} ]
}
```

---
### 3. Login Programador
**GET** `https://ibagueaprendetic.ibague.gov.co/api/v1/Auth.php`



**Encabezados:**
- `Authorization: Bearer {token}`

**Respuesta:**
```json
{
  "status": "success",
  "token": "...",
  "expires_at": "2024-03-01 12:00:00"
}
```

---
### 4. Crear Programador

## Necesario Crear un token el moodle para manejar la seguridad del API Especialmente evitar Riegos de Seguridad y evitar que personas no autorizadas ingrese y registre nuevos programadores o que les asigne permisos no autorizados por el administrador del moodle.

**POST** `https://ibagueaprendetic.ibague.gov.co/api/v1/Developers.php`

**Body JSON:**
```json
{
  "username": "dev_user",
  "password": "secure_pass"
}
```

**Respuesta:**
```json
{
  "status": "success",
  "message": "Programador creado"
}
```

---
### 5. Listar Programadores
**GET** `https://ibagueaprendetic.ibague.gov.co/api/v1/GetUsers.php`

**Respuesta:**
```json
{
  "status": "success",
  "developers": [ {...} ]
}
```

---
### 6. Actualizar Permisos de Programadores
**PUT** `https://ibagueaprendetic.ibague.gov.co/api/v1/Developers.php`

**Body JSON:**
```json
{
  "id": 1,
  "status": "active"
}
```

**Respuesta:**
```json
{
  "status": "success",
  "message": "Estado actualizado"
}
```

## Seguridad
- La mayoría de las rutas están protegidas mediante token o sesión de Moodle.
- Se recomienda utilizar `Authorization: Bearer {token}` en los encabezados.

## Autor
**Jose Palacio**

