# SIGPA — Sistema Integral de Gestión de Préstamos Académicos

Sistema web desarrollado en **Laravel 12** para la **Fundación Universitaria Claretiana (Quibdó, Colombia)**, orientado a la gestión y control del préstamo de aulas y equipos tecnológicos.

---

## Módulos implementados

### Autenticación y acceso
- Login con control de roles (Admin, Secretaría, Técnico TI, Docente)
- Recuperación de contraseña por correo electrónico (Resend)
- Middleware de protección por rol (`role:admin`, `role:secretaria`, `role:tecnico`, `role:docente`)
- Redirección automática al dashboard correspondiente según el rol
- **Flujo de primer acceso:** cuando el administrador crea un usuario, el sistema genera una contraseña temporal automáticamente, la envía al correo del usuario y al ingresar por primera vez es redirigido obligatoriamente a cambiar su contraseña antes de acceder al sistema

### Administrador
- Dashboard con estadísticas generales del sistema
- Gestión de **usuarios** (crear, editar, activar/desactivar)
  - La creación genera una contraseña temporal aleatoria y la envía por correo electrónico al nuevo usuario
  - La contraseña temporal también se muestra en pantalla al administrador como respaldo
- Gestión de **aulas** (crear, editar, eliminar)
- Gestión de horarios por aula (asignar, activar/desactivar, eliminar)
- Gestión de **equipos tecnológicos** (CRUD completo con validación de código de inventario único)
- Gestión de **docentes** como entidades sin acceso al sistema (RF39): registro con contraseña bloqueada, actualización y eliminación (con bloqueo si tiene préstamos activos)
- Gestión de **roles** (visualización y detalle)
- Aprobación y cancelación de préstamos desde el panel admin
- **Reportes** de préstamos con exportación
- Página de configuración del sistema

### Secretaría
- Dashboard con resumen de préstamos del día
- **Préstamos de aulas**: solicitar, aprobar, cancelar, check-in y finalizar
- Vista de aulas y su disponibilidad
- Historial de préstamos realizados
- **Préstamos de equipos** (HU-09 / HU-10):
  - Registrar préstamo independiente de equipo a docente
  - Aprobar, entregar, devolver (con registro de estado físico) y cancelar
  - Asignar equipo directamente a un préstamo de aula activo
  - Validación de disponibilidad y conflictos de horario

### Técnico TI
- Dashboard con estadísticas en tiempo real: total de equipos, disponibles, en préstamo, dañados
- Vista de **asignaciones del día**: préstamos de aula activos con sus equipos asignados
  - Asignar equipo a un préstamo de aula (HU-09)
  - Registrar devolución con estado físico (bueno / regular / dañado)
- **Inventario de equipos** (HU-10 / HU-12):
  - Tabs de filtrado: Todos / Disponibles / Prestados / Fuera de servicio
  - Búsqueda por nombre, código o marca
  - Paginación de 10 registros por página
  - Modal **Ver detalle**: ficha técnica completa (marca, modelo, descripción, ubicación, fecha de adquisición, asignación activa)
  - Modal **Registrar equipo**: nuevo implemento con todos sus atributos
  - Modal **Cambiar estado físico**: selector visual (Bueno / Regular / Dañado / Dado de baja), observación con contador de 200 caracteres y barra de progreso, ubicación de almacenamiento
    - La observación se guarda en el campo `descripcion` del equipo
    - **Bloqueado** si el equipo tiene un préstamo activo (botón deshabilitado en UI + validación en backend)
  - Disponibilidad calculada automáticamente: `dañado` o `dado_de_baja` → no disponible; recuperación solo si no tiene préstamo activo

### Docente
- Dashboard con resumen personal: estadísticas de clases de la semana, solicitudes pendientes y aprobadas, últimas 5 solicitudes recientes y horario semanal integrado
- **Horario semanal navegable**: vista completa de clases por día (lunes-sábado) con navegación por semanas (±52 semanas desde la actual), indicador de día actual, materia, horario, salón y grupo
- **Solicitudes de préstamo de aula**:
  - Crear solicitud: selecciona salón, fecha (no pasada), hora inicio/fin y motivo opcional
  - Validación de conflictos contra horarios académicos y otros préstamos activos/pendientes
  - Listar todas las solicitudes propias con filtros por estado y fecha (paginado)
  - Cancelar solicitudes propias en estado pendiente (con confirmación)
  - Estados: pendiente, aprobada, en uso, finalizada, cancelada
- Sidebar con badge numérico de solicitudes pendientes actualizado en cada carga

---

## Roles del sistema

| Rol | Descripción |
|---|---|
| Administrador | Control total: usuarios, aulas, equipos, roles, reportes, docentes |
| Secretaría | Gestión de préstamos de aulas y equipos |
| Técnico TI | Asignación de equipos, inventario y control de estado físico |
| Docente | Consulta su horario semanal y gestiona solicitudes de préstamo de aula |

---

## Requisitos

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- XAMPP / Laragon / Laravel Sail (o servidor equivalente)
- Cuenta en [Resend](https://resend.com) para el envío de correos

---

## Descarga del proyecto

### Opción 1 — Git (recomendado)

Asegúrate de tener [Git](https://git-scm.com/) instalado y ejecuta:

```bash
git clone https://github.com/Ferpacios23/SIGPA.git
```

### Opción 2 — ZIP desde GitHub

1. Ingresa al repositorio: [https://github.com/Ferpacios23/SIGPA](https://github.com/Ferpacios23/SIGPA)
2. Haz clic en el botón **Code**
3. Selecciona **Download ZIP**
4. Extrae el contenido en tu carpeta de proyectos (ej. `htdocs/` si usas XAMPP)

---

## Instalación

```bash
# 1. Entrar a la carpeta del proyecto
cd SIGPA

# 2. Instalar dependencias
composer install

# 3. Copiar el archivo de entorno
cp .env.example .env

# 4. Generar la clave de la aplicación
php artisan key:generate

# 5. Configurar la base de datos y el correo en .env
DB_DATABASE=sigpa
DB_USERNAME=root
DB_PASSWORD=

RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxxxx
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="sigpa@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Iniciar el servidor de desarrollo
php artisan serve
```

---

## Configuración de correo (Resend)

El sistema usa [Resend](https://resend.com) para enviar correos electrónicos (contraseñas temporales y recuperación de contraseña).

1. Crea una cuenta en [resend.com](https://resend.com)
2. Genera una API Key en **API Keys**
3. Agrega un dominio verificado o usa `onboarding@resend.dev` para pruebas (solo envía al correo con el que te registraste en Resend)
4. Configura las variables de entorno en `.env`:

```ini
RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxxxx
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="sigpa@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Usuarios, Aulas, Equipos, Roles, Reportes, Docentes, Horarios
│   │   ├── Auth/               # Login, recuperación de contraseña y cambio de contraseña obligatorio
│   │   ├── Dashboard/          # Paneles por rol (Admin, Secretaría, Técnico, Docente) + HorarioDocenteController
│   │   └── Secretaria/         # Préstamos de equipos (secretaría)
│   └── Middleware/
│       ├── RoleMiddleware.php          # Protección por rol
│       ├── CheckPasswordChange.php     # Redirige al cambio de contraseña en primer acceso
│       └── NoCacheHeaders.php
├── Mail/
│   └── TempPasswordMail.php    # Mailable para envío de contraseña temporal al nuevo usuario
├── Models/
│   ├── User.php
│   ├── UserProfile.php
│   ├── Role.php
│   ├── Aula.php
│   ├── Equipo.php
│   ├── PrestamoAula.php
│   └── PrestamoEquipo.php
database/
├── migrations/
└── seeders/
resources/
└── views/
    ├── admin/                  # Usuarios, aulas, equipos, roles, docentes, reportes
    ├── auth/                   # Login, recuperación y cambio de contraseña
    ├── dashboard/              # Dashboards por rol
    ├── emails/                 # Plantillas de correo electrónico
    │   └── temp_password.blade.php
    ├── layouts/                # Layout principal del dashboard
    ├── secretaria/             # Préstamos de aulas y equipos
    ├── tecnico/                # Asignaciones e inventario TI
    └── docente/                # Dashboard, horario semanal y solicitudes de aula
```

---

## Stack tecnológico

- **Backend:** Laravel 12 (PHP 8.4)
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Base de datos:** MySQL / MariaDB
- **Correo electrónico:** Resend (`resend/resend-laravel`)
- **Servidor local:** XAMPP (Apache + MySQL)

---

## Tablas principales

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del sistema (incluye `must_change_password`) |
| `user_profiles` | Perfil extendido (rol, datos académicos) |
| `roles` | Roles del sistema |
| `aulas` | Aulas disponibles para préstamo |
| `equipos` | Inventario de equipos tecnológicos |
| `prestamos_aulas` | Préstamos de aulas |
| `prestamos_equipos` | Préstamos de equipos (independientes o vinculados a un préstamo de aula) |
| `horarios` | Horarios disponibles por aula |
| `historial_movimientos` | Auditoría de todas las acciones del sistema |

---

## Licencia

Este proyecto es de uso académico/institucional — Fundación Universitaria Claretiana, Quibdó, Colombia.
