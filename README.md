# MediHonorarios (Laravel 13 + Vue 3)

Sistema web para clínicas y centros médicos privados que sirve para gestionar y liquidar los honorarios de los médicos que cobran según lo que atienden. Este repositorio tiene la **administración (MVC)** que alimenta el core del proyecto: el motor de liquidación neta de honorarios.

Materia: ISWZ3101 Ingeniería Web (UDLA).

> **Estado del proyecto:** el proyecto avanza por entregas. En esta entrega está el login, los roles y el CRUD de administración. El motor de liquidación y el módulo de estadísticas se agregan en las siguientes entregas.

---

## Qué hace el sistema

- **Login por sesión** con contraseñas encriptadas con bcrypt (`Hash::make`).
- **Tres roles:** `admin`, `gerencia` y `medico`. Hoy el único con pantallas es el administrador; gerencia y médico entran, pero sus pantallas llegan con el core.
- **Administración (solo admin):** CRUD de usuarios, médicos, especialidades, reglas de honorarios, recursos de la clínica, atenciones y usos de recurso.
- **Validación en backend de un dato sensible:** la **cédula del médico** se valida en el servidor antes de guardarla (ver más abajo).
- **Dropdowns dependientes:** nunca se escribe una clave foránea a mano.

## Tecnologías

| Parte | Tecnología |
| :--- | :--- |
| Backend | Laravel 13 (PHP 8.3 o superior) |
| Frontend | Vue 3 (Composition API, `<script setup>`), Axios, Vite |
| Base de datos | MySQL en local, PostgreSQL en el despliegue |
| Despliegue | Docker en Render |

---

## Arquitectura MVC

| Capa | Ubicación | Descripción |
| :--- | :--- | :--- |
| Modelo | `app/Models/` | `User`, `Medico`, `Especialidad`, `ReglaHonorario`, `Recurso`, `Atencion`, `UsoRecurso` con sus relaciones Eloquent. |
| Vista | `resources/views/app.blade.php` y `resources/js/` | Blade solo da el contenedor `#app`; Vue pinta toda la interfaz (`App.vue` y `components/`). |
| Controlador | `app/Http/Controllers/` | Reciben la petición, validan y guardan. Uno por tabla, más `AuthController`. |
| Middleware | `app/Http/Middleware/RoleMiddleware.php` | Deja pasar solo a los roles indicados, por ejemplo `role:admin`. Si no, responde 403. |
| Regla de validación | `app/Rules/CedulaEcuatoriana.php` | Valida la cédula con el algoritmo oficial. |

## Estructura

```text
app/
  Http/Controllers/   AuthController, UserController, MedicoController, EspecialidadController,
                      ReglaHonorarioController, RecursoController, AtencionController, UsoRecursoController
  Http/Middleware/    RoleMiddleware
  Models/             User, Medico, Especialidad, ReglaHonorario, Recurso, Atencion, UsoRecurso
  Rules/              CedulaEcuatoriana
bootstrap/app.php     alias 'role' y trustProxies (para HTTPS detrás de Render)
database/
  migrations/         users (+ rol), especialidades, medicos, reglas_honorarios, recursos, atenciones, usos_recurso
  seeders/            UsuariosSeeder, ClinicaSeeder, DatabaseSeeder
lang/es/validation.php   mensajes de validación en español
resources/
  css/app.css
  js/App.vue          elige la pantalla según la sesión y el rol
  js/errores.js       saca el mensaje de error que manda Laravel
  js/components/      Login.vue y admin/ (un componente por tabla)
routes/web.php        todas las rutas (con sesión, sin api.php)
Dockerfile, docker-start.sh, .dockerignore
```

---

## Base de datos

| Tabla | Campos principales | Relaciones |
| :--- | :--- | :--- |
| `users` | name, email (único), password, role (`admin`, `gerencia`, `medico`) | |
| `especialidades` | nombre (único) | tiene muchos médicos |
| `medicos` | nombre, cedula (única), tipo_vinculacion (`externo` o `planta`), user_id (opcional) | pertenece a una especialidad y, opcionalmente, a un usuario |
| `reglas_honorarios` | umbral (único), porcentaje | sin llaves foráneas (tabla de parámetros) |
| `recursos` | nombre, tarifa_hora | tiene muchos usos |
| `atenciones` | fecha_atencion, servicio, monto_cobrado | pertenece a un médico |
| `usos_recurso` | fecha_uso, horas | pertenece a un médico y a un recurso |

El porcentaje de cada regla es **lo que se lleva el médico** (por ejemplo, 70 significa que el médico recibe el 70 % de la atención).

---

## Lo que pide la tarea

### 1. Validación en backend de un dato sensible: la cédula del médico

La cédula se valida en el servidor, en `MedicoController`, antes de guardar en la base de datos. La regla está en `app/Rules/CedulaEcuatoriana.php` y comprueba:

- que tenga exactamente 10 dígitos;
- que los 2 primeros dígitos (provincia) estén entre 01 y 24 y que el tercero sea menor a 6;
- que el último dígito sea igual al dígito verificador (módulo 10);
- que no esté repetida (`unique`).

Así, aunque alguien se salte la pantalla y mande la petición a mano, el servidor rechaza la cédula inválida con un error 422.

### 2. Dropdowns dependientes (nada de claves foráneas a mano)

- **Atenciones:** primero se escoge la **especialidad** y recién ahí se habilita el dropdown de **médicos**, que se llena con los médicos de esa especialidad (`GET /api/medicos?especialidad_id=`). Al cambiar de especialidad se borra el médico escogido.
- **Usos de recurso:** el médico sale de un dropdown que solo trae médicos **externos** (a los de planta no se les cobra), y el recurso sale de otro dropdown.
- **Médicos:** la especialidad y el usuario enlazado salen de dropdowns.

### 3. Otras reglas de negocio validadas en el servidor

- Un uso de recurso solo se acepta para médicos externos.
- No se puede eliminar un médico, especialidad o recurso que ya tenga datos asociados (responde 409).
- Siempre debe existir una regla con umbral 0: esa regla no se puede borrar ni cambiar de umbral.
- Un admin no puede eliminar su propio usuario.

---

## Rutas

Todas están en `routes/web.php`. El login es por sesión, por eso no se usa `api.php`.

| Método | Ruta | Quién | Descripción |
| :--- | :--- | :--- | :--- |
| `POST` | `/auth/login` | público | Inicia sesión |
| `POST` | `/auth/logout` | público | Cierra sesión |
| `GET` | `/auth/me` | público | Dice si hay sesión, el nombre y el rol |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/usuarios` | admin | CRUD de usuarios |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/medicos` | admin | CRUD de médicos (filtros `especialidad_id` y `tipo_vinculacion`) |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/especialidades` | admin | CRUD de especialidades |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/reglas-honorarios` | admin | CRUD de reglas |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/recursos` | admin | CRUD de recursos |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/atenciones` | admin | CRUD de atenciones |
| `GET`, `POST`, `PUT`, `DELETE` | `/api/usos-recurso` | admin | CRUD de usos de recurso |

Las rutas `/api/*` pasan por `auth` y `role:admin`. Sin sesión responden 401 y con otro rol, 403.

---

## Instalación local

Requisitos: PHP 8.3 o superior, Composer, Node.js y MySQL (por ejemplo con Laragon).

1. Clonar el repositorio e instalar dependencias:

   ```bash
   composer install
   npm install
   ```

2. Crear el archivo de entorno y la clave:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   En Windows PowerShell el primer comando es `copy .env.example .env`.

3. Crear la base de datos `medihonorarios` en MySQL (con HeidiSQL o con el comando de abajo). El `.env.example` ya apunta a ella con usuario `root` sin contraseña.

   ```bash
   mysql -u root -e "CREATE DATABASE medihonorarios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   ```

4. Crear las tablas y los datos de prueba:

   ```bash
   php artisan migrate --seed
   ```

5. Levantar los dos servidores, cada uno en su terminal:

   ```bash
   php artisan serve
   ```

   ```bash
   npm run dev
   ```

6. Abrir `http://localhost:8000`.

## Usuarios de prueba

Los crea el seeder. Todos tienen la contraseña `1234`.

| Rol | Correo | Qué ve hoy |
| :--- | :--- | :--- |
| Administrador | `admin@test.com` | Panel completo de administración |
| Gerencia | `gerencia@test.com` | Mensaje de "pronto" (sus pantallas llegan con el core) |
| Médico | `medico@test.com` | Mensaje de "pronto" (está enlazado al Dr. Carlos Mena) |

Los datos de ejemplo incluyen 5 médicos (3 externos y 2 de planta, con cédulas válidas), las reglas de honorarios 0 → 50 %, 30 → 60 %, 80 → 70 % y 200 → 80 %, tres recursos y las atenciones de la quincena del 01/10/2026 al 15/10/2026.

**Valor de control para el core:** con esos datos, la liquidación del Dr. Carlos Mena en esa quincena debe dar bruto $302.50, descuento $80.00 (quirófano, 2 h × $40) y neto a transferir $222.50.

---

## Despliegue con Docker (Render)

El `Dockerfile` tiene dos etapas: Node 22 compila el frontend y PHP 8.4 con Apache sirve la app desde `public/`. El script `docker-start.sh`:

1. ajusta el puerto de Apache con la variable `PORT`;
2. cachea la configuración (`config:cache`);
3. corre las migraciones (`migrate --force`);
4. corre los seeders solo si `RUN_SEED=true`.

Variables de entorno que se configuran en el panel de Render (las claves secretas nunca van en el repositorio):

| Variable | Valor |
| :--- | :--- |
| `APP_NAME` | `MediHonorarios` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | la clave generada con `php artisan key:generate --show` |
| `APP_URL` | la URL de la app en Render |
| `APP_LOCALE` | `es` (sin esto los errores salen en inglés) |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | la URL de conexión de la base PostgreSQL de Render |
| `SESSION_DRIVER` | `database` |
| `LOG_CHANNEL` | `stderr` |
| `RUN_SEED` | `true` solo en el primer despliegue; después `false` para no duplicar datos |

---

## Enlaces

- Aplicación desplegada: _pendiente_
- Video explicativo: _pendiente_

## Autor

**Samuel Chalá**
Estudiante de Ingeniería en Software, Universidad de Las Américas (UDLA).
