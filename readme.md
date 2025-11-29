# 🏋️ AlixSport — Plataforma web para venta de suplementos deportivos

AlixSport es una aplicación web Full-Stack diseñada para la venta de suplementos deportivos.
 El sistema está compuesto por:

- **Backend en PHP** con Composer
- **Frontend en React + Vite**
- **Base de datos MySQL**

El proyecto permite gestionar productos, usuarios, facturación, carrito de compras y funcionalidades adicionales como envío de correos y generación de PDF.

------

## 🚀 Tecnologías principales

| Área          | Tecnologías                                    |
| ------------- | ---------------------------------------------- |
| Backend       | PHP 8+, Composer, PHPMailer, DOMPDF, Dotenv    |
| Frontend      | React, Vite, TailwindCSS, Axios, Framer Motion |
| Base de datos | MySQL                                          |
| Ejecución     | Node.js & npm, PHP CLI                         |

------

## 📌 Requisitos previos

Asegúrate de tener instalados:

| Herramienta    | Versión recomendada |
| -------------- | ------------------- |
| PHP            | 8.0+                |
| Composer       | Última versión      |
| Node.js + npm  | 18 o superior       |
| MySQL          | 5.7 / 8.0           |
| Git (opcional) | —                   |

------

## 🗄️ Base de datos

La BD se encuentra en:

```
backend/api/config/AlixSport.sql
```

Importa ese archivo en tu servidor MySQL antes de ejecutar el backend.

------

## ⚙️ Configuración de variables de entorno (.env)

Son necesarios **dos archivos .env**: uno en `backend` y otro en `frontend`.

------

### 🔹 .env para **Backend**

Crear archivo en: `backend/.env`

```
HOSTNAME=127.19.8.7
PORT_BACKEND=5174
PORT_FRONT=5173
DB_HOST=127.0.0.1
DB_PORT=5501
DB_USER=root
DB_PASS=1987
DB_NAME=AlixSport_db
URL_CORREO=alixsport.suplementos.contacto@gmail.com
NOMBRE_CORREO=AlixSport
PASSWORD_CORREO=sppsrylkcxtuszjq
```

📌 **Solo deben cambiar las credenciales de la base de datos** (DB_HOST, DB_PORT, DB_USER, DB_PASS según tu entorno).

------

### 🔹 .env para **Frontend**

Crear archivo en: `frontend/.env`

```
VITE_HOSTNAME="127.19.8.7"
VITE_PORT_FRONT=5173
VITE_API="http://127.19.8.7:5174"
```

------

## 🛠 Instalación

### 1️⃣ Clonar el repositorio (si aplica)

```
git clone https://github.com/JuanJoseDuranRinconCAMPUS2/AlixSport
```

------

### 2️⃣ Instalar dependencias del Backend

```
cd backend
composer install
```

------

### 3️⃣ Instalar dependencias del Frontend

```
cd frontend
npm install
```

------

## ▶️ Ejecución del proyecto

Antes de ejecutar, asegúrate de que la base de datos ya esté importada.

### 🔹 Iniciar backend (PHP)

Desde `/backend`:

```
php run.php
```

------

### 🔹 Iniciar frontend (React)

Desde `/frontend`:

```
npm run dev
```

------

### 🔹 Ejecutar ambos al mismo tiempo (opcional)

Desde `/frontend`:

```
npm run start:all
```

------

## 📂 Estructura del proyecto

```
/backend
   ├── api
   ├── src
   ├── run.php
   ├── .env
   └── composer.json

/frontend
   ├── src
   ├── public
   ├── .env
   └── package.json
```

------

## 🤝 Contribución

Cualquier mejora al código o ideas para nuevas funcionalidades son bienvenidas.
 Para cambios mayores, crea un branch y luego un Pull Request.

------

## 👨‍💻 Autores

**Juan Jose Duran Rincon**
 📧 Email: jduranrinconcampus@gmail.com GitHub: [JuanJoseDuranRinconCAMPUS2 (Juan Jose Duran Rincon)](https://github.com/JuanJoseDuranRinconCAMPUS2)

**Jhon Mario Ardila Perez**
 📧 Email: ardilaperezjhonmario75@gmail.com GitHub: [jhonmario75](https://github.com/jhonmario75)

