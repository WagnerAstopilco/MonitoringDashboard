# Monitoring Dashboard

Sistema web de gestión y monitoreo desarrollado como proyecto de portfolio, orientado a la administración de servicios, promociones, clientes, usuarios y transacciones.

## 🌐 Demo

Puedes probar la aplicación desde el siguiente enlace:

**[Ver Demo](https://monitoringdashboard.wagner-asto-30.workers.dev/demo)**

> La demo utiliza un usuario de acceso limitado para permitir la exploración de las principales funcionalidades del sistema sin necesidad de crear una cuenta.

## 📌 Características

- 🔐 Autenticación mediante Laravel Sanctum
- 👥 Gestión de usuarios y roles
- 🛡️ Control de acceso mediante permisos
- 👤 Gestión de clientes
- 🛠️ Gestión de servicios
- 🎁 Gestión de promociones
- 💰 Gestión de transacciones
- 💳 Registro y seguimiento de pagos
- 📊 Dashboard administrativo
- 📱 Interfaz responsive
- 📄 Exportación de información
- 🔎 Búsqueda y filtrado de información
- ⚡ API REST para comunicación entre frontend y backend

## 🏗️ Arquitectura

El proyecto está dividido en dos aplicaciones principales:

### Frontend

Aplicación SPA desarrollada con Vue.js.

```text
Vue.js
   │
   ├── Views
   ├── Components
   ├── Stores
   ├── Services
   └── Vue Router
         │
         ▼
      Laravel API
```

### Backend

API REST desarrollada con Laravel.

```text
Laravel
    │
    ├── Controllers
    ├── Models
    ├── Requests
    ├── Resources
    ├── Policies / Permissions
    └── API Routes
          │
          ▼
       MySQL
```

## 🧰 Tecnologías

### Frontend

- Vue.js
- Vite
- Pinia
- Vue Router
- Axios
- Bootstrap
- DataTables
- SweetAlert2

### Backend

- PHP
- Laravel
- Laravel Sanctum
- Spatie Laravel Permission
- Laravel Excel

### Base de datos

- MySQL

## 🔐 Autenticación y autorización

La API utiliza Laravel Sanctum para la autenticación mediante tokens.

El sistema implementa autorización basada en roles y permisos mediante Spatie Laravel Permission.

Ejemplo de permisos utilizados:

```text
dashboard.view
salesboard.view
clients.view
services.view
services.create
promotions.view
promotions.create
transactions.view
transactions.create
users.view
```

El frontend utiliza estos permisos para controlar la visualización de determinadas opciones de la interfaz, mientras que el backend mantiene la validación de autorización sobre los recursos protegidos.

## 🚀 Instalación

### Requisitos

- PHP 8.x
- Composer
- Node.js
- MySQL
- Git

### Backend

Clonar el repositorio:

```bash
git clone https://github.com/WagnerAstopilco/MonitoringDashboard.git
```

Ingresar al directorio del backend:

```bash
cd monitoring-dashboard
```

Instalar las dependencias:

```bash
composer install
```

Crear el archivo de entorno:

```bash
cp .env.example .env
```

Generar la clave de Laravel:

```bash
php artisan key:generate
```

Configurar las variables de conexión a MySQL en el archivo `.env`.

Ejecutar las migraciones:

```bash
php artisan migrate
```

Ejecutar los seeders, si corresponde:

```bash
php artisan db:seed
```

Iniciar el servidor de desarrollo:

```bash
php artisan serve
```

### Frontend

Ingresar al directorio del frontend:

```bash
cd frontend
```

Instalar las dependencias:

```bash
npm install
```

Iniciar el entorno de desarrollo:

```bash
npm run dev
```

Para generar la versión de producción:

```bash
npm run build
```

## ⚙️ Variables de entorno

El proyecto utiliza variables de entorno para las credenciales y configuración de los diferentes servicios.

Ejemplo:

```env
APP_NAME=MonitoringDashboard
APP_ENV=local
APP_KEY=
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

**Las credenciales reales no forman parte del repositorio.**

Para producción, estas variables se configuran directamente en el proveedor de hosting.

## ☁️ Despliegue

La versión utilizada para demostración se encuentra desplegada utilizando servicios cloud.

```text
                   ┌──────────────────┐
                   │    Portfolio     │
                   └────────┬─────────┘
                            │
                            ▼
                   ┌──────────────────┐
                   │ Vue.js Frontend  │
                   └────────┬─────────┘
                            │ HTTPS
                            ▼
                   ┌──────────────────┐
                   │   Laravel API    │
                   └────────┬─────────┘
                            │
                            ▼
                   ┌──────────────────┐
                   │      MySQL       │
                   └──────────────────┘
```

## 📂 Estructura principal

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   └── Models/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── routes/
│   └── api.php
│
├── resources/
├── public/
├── storage/
│
├── .env.example
├── composer.json
└── README.md
```

## 🔒 Seguridad

Este repositorio es público con fines demostrativos.

No se incluyen:

- Contraseñas de producción
- Claves privadas
- API keys
- Tokens de acceso
- Credenciales de base de datos
- Variables `.env` de producción

Las credenciales necesarias para el despliegue se mantienen en las variables de entorno del servidor.

## 📚 Objetivo del proyecto

Este proyecto forma parte de mi portfolio de desarrollo web y tiene como objetivo demostrar la implementación de una aplicación administrativa completa utilizando una arquitectura frontend/backend separada.

El proyecto abarca desde el diseño de la base de datos y desarrollo de una API REST hasta autenticación, autorización, gestión de información y despliegue en un entorno cloud.

## 👨‍💻 Autor

**Wagner Lanteiner Astopilco Chuquiruna**

Desarrollador de software enfocado en desarrollo web con Laravel, Vue.js y MySQL.
