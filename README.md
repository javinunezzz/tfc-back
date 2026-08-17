<div align="center">

# NoteShare API

### API REST para una plataforma de publicación y gestión de apuntes

<p>
  <img src="https://img.shields.io/badge/Laravel-12-0D1117?style=for-the-badge&logo=laravel&logoColor=26F766" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.2%2B-0D1117?style=for-the-badge&logo=php&logoColor=26F766" alt="PHP 8.2 o superior" />
  <img src="https://img.shields.io/badge/MySQL-Database-0D1117?style=for-the-badge&logo=mysql&logoColor=26F766" alt="MySQL" />
  <img src="https://img.shields.io/badge/Sanctum-Auth-0D1117?style=for-the-badge&logo=laravel&logoColor=26F766" alt="Laravel Sanctum" />
</p>

</div>

---

## Descripción

Este repositorio contiene el backend de **NoteShare**, una plataforma web para que estudiantes publiquen, organicen, consulten y descarguen apuntes en PDF.

La aplicación expone una API REST desarrollada con Laravel e incorpora autenticación, permisos por roles, gestión de contenido, administración, suscripciones y estadísticas.

## Funcionalidades

- Registro, inicio de sesión y autenticación mediante Laravel Sanctum.
- Verificación de correo electrónico y recuperación de contraseña.
- Control de acceso basado en roles: `ADMIN`, `PREMIUM` y `FREE`.
- CRUD de usuarios, categorías, asignaturas y apuntes.
- Publicación, búsqueda, filtrado, edición y descarga de apuntes.
- Gestión de favoritos, descargas, suscripciones y estadísticas personales.
- Panel administrativo con operaciones específicas para cada recurso.
- Gestión de archivos PDF y generación de documentos con DomPDF.
- Integraciones preparadas con PayPal y Resend.
- Entorno de datos con MySQL y configuración mediante Docker Compose.

## Stack

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 |
| Lenguaje | PHP 8.2+ |
| Autenticación | Laravel Sanctum |
| Roles y permisos | Spatie Laravel Permission |
| Base de datos | MySQL / SQLite |
| PDF | Laravel DomPDF |
| Integraciones | PayPal · Resend |
| Infraestructura | Docker · Railway |

## Requisitos

- PHP 8.2 o superior
- Composer
- MySQL o SQLite
- Extensiones de PHP requeridas por Laravel

## Instalación local

```bash
git clone https://github.com/javinunezzz/tfc-back.git
cd tfc-back

composer install
cp .env.example .env
php artisan key:generate
```

Configura la conexión a la base de datos en `.env` y ejecuta:

```bash
php artisan migrate
php artisan serve
```

La API quedará disponible, por defecto, en:

```text
http://127.0.0.1:8000
```

## Grupos principales de endpoints

| Grupo | Ejemplos |
|---|---|
| Autenticación | Registro, login, verificación de email y recuperación de contraseña |
| Usuarios | Consulta, actualización, eliminación y búsquedas administrativas |
| Apuntes | CRUD, filtros, búsqueda, recientes, descarga y estadísticas |
| Catálogo | Categorías y asignaturas |
| Producto | Favoritos, suscripciones y descargas |
| Administración | Gestión completa de usuarios, contenido y configuración |

> Las rutas protegidas requieren autenticación y, según el endpoint, un rol autorizado.

## Frontend

El cliente web de NoteShare está disponible en:

[github.com/javinunezzz/tfc-front](https://github.com/javinunezzz/tfc-front)

## Estado del proyecto

Proyecto académico desarrollado como trabajo final de **Desarrollo de Aplicaciones Web (DAW)**.
