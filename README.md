# 🍳 Blog de Recetas de Cocina — WordPress de Alto Rendimiento

> Proyecto WordPress moderno, ligero y modular enfocado en la experiencia culinaria (UX de cocina), optimización para motores de búsqueda con **Schema.org/Recipe (JSON-LD)** y cumplimiento estricto de **Core Web Vitals** (LCP < 1.5s, sin constructores pesados).

---

## 🧭 Guías de Instalación y Configuración por Sistema Operativo

Para instrucciones detalladas paso a paso desde cero para tu sistema operativo específico, consulta las guías dedicadas:

| Sistema Operativo | Entornos Soportados | Enlace Directo |
| :--- | :--- | :---: |
| 🐧 **Linux** | CachyOS, Arch Linux, Ubuntu, Debian, Fedora, Mint | [📖 **Ver Guía para Linux**](docs/README_LINUX.md) |
| 🍏 **macOS** | Apple Silicon (M1, M2, M3, M4) e Intel (Homebrew) | [📖 **Ver Guía para macOS**](docs/README_MACOS.md) |
| 🪟 **Windows** | WSL2 (Ubuntu / Debian), LocalWP (1 Clic) y PowerShell | [📖 **Ver Guía para Windows**](docs/README_WINDOWS.md) |

---

## 📌 Parámetros de Conexión y Acceso Rápido

| Parámetro | Valor / Enlace por Defecto |
| :--- | :--- |
| **URL del Sitio Público:** | [http://localhost:8080](http://localhost:8080) |
| **Panel de Administración:** | [http://localhost:8080/wp-admin](http://localhost:8080/wp-admin) |
| **Usuario Administrador:** | `dev` *(o el configurado en la instalación)* |
| **Nombre de Base de Datos:** | `misitio_db` |
| **Usuario de Base de Datos:** | `admin` |
| **Contraseña de Base de Datos:** | `secreto123` |
| **Host de Base de Datos:** | `localhost` *(o `127.0.0.1` en macOS/Docker)* |
| **Servidor Web Local:** | PHP Built-in Server vía WP-CLI (`wp server --port=8080`) |
| **Idioma por defecto:** | Español (`es_ES`) |

---

## 🚀 Instalación Rápida Desde Cero (Resumen Global)

Si ya cuentas con **PHP 8.1+**, **MariaDB/MySQL** y **WP-CLI** instalados en tu sistema:

```bash
# 1. Situarse en la carpeta del proyecto
cd ~/Escritorio/mi-sitio

# 2. Iniciar el servicio de base de datos (según tu sistema operativo)
sudo systemctl start mariadb   # Linux (systemd)
brew services start mariadb   # macOS (Homebrew)
sudo service mariadb start    # Windows (WSL2)

# 3. Crear base de datos y usuario (si es una instalación nueva)
mariadb -u root -e "CREATE DATABASE IF NOT EXISTS misitio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'secreto123'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost'; FLUSH PRIVILEGES;"

# 4. Generar configuración de WordPress
wp config create --dbname=misitio_db --dbuser=admin --dbpass=secreto123 --dbhost=localhost --locale=es_ES

# 5. Instalar WordPress e idioma español
wp core install --url=http://localhost:8080 --title="Blog de Recetas" --admin_user=dev --admin_password="PasswordSegura2026!" --admin_email=dev@misitio.local --skip-email
wp language core install es_ES --activate

# 6. Arrancar el servidor web
wp server --port=8080
```

---

## 💻 Flujo de Trabajo Diario

### 1. Iniciar la jornada de trabajo
Abre una terminal y ejecuta:
```bash
cd ~/Escritorio/mi-sitio
wp server --port=8080
```
Mantén esa terminal abierta mientras programas. El servidor responderá en [http://localhost:8080](http://localhost:8080).

### 2. Detener el servidor
Presiona **`Ctrl + C`** en la terminal donde corre el servidor.

### 3. Verificar estado de la base de datos
Si recibes un error `Error establishing a database connection`, arranca MariaDB:
* **Linux (Arch/Ubuntu):** `sudo systemctl start mariadb`
* **macOS:** `brew services start mariadb`
* **Windows (WSL2):** `sudo service mariadb start`

---

## 🎨 Temas, publicación y despliegue con WP-CLI

Desde la carpeta del proyecto, WP-CLI permite cambiar el tema activo, crear borradores y publicar páginas o entradas. Antes de cambios importantes, exporta la base de datos:

```bash
wp db export backup-antes-del-cambio.sql
wp theme list
wp theme activate twentytwentyfive
```

Para publicar contenido, créalo como borrador y cambia su estado cuando lo hayas revisado:

```bash
wp post create --post_type=page --post_title="Acerca de este sitio" --post_status=draft
# Sustituye 42 por el ID que devolvió el comando anterior
wp post update 42 --post_status=publish
```

Cambiar el estado publica esa página o entrada, pero **no pone el sitio en Internet**. Para desplegar el sitio se necesita alojamiento compatible con WordPress, dominio, HTTPS, archivos del sitio y migración de la base de datos. `wp server --port=8080` es solo para desarrollo local, no para producción. La [guía multiplataforma](GUIA_MULTIPLATAFORMA.md) contiene los pasos y comandos para cambiar temas y preparar contenido o una migración a producción.

---

## 🏛️ Arquitectura y Gobernanza del Proyecto

El desarrollo de este blog sigue el protocolo de arquitectura técnica documentado en los siguientes archivos clave:

* 🤖 [**AGENTS.md**](AGENTS.md): Define la matriz de roles de agentes (Lead Architect, WordPress Backend, UI/UX Frontend, SEO & Schema Specialist, QA & Security), principios de codificación WPCS y reglas de oro de seguridad y rendimiento.
* 📋 [**tasks.md**](tasks.md): Backlog maestro dividido en 5 fases:
  1. *Fase 1: Arquitectura Base y CPT `receta`*.
  2. *Fase 2: Schema.org JSON-LD y SEO Gastronómico*.
  3. *Fase 3: UX de Cocina Interactiva (Modo Cocina WakeLock, escalador de porciones, print CSS)*.
  4. *Fase 4: Bloques Gutenberg y Plantillas FSE*.
  5. *Fase 5: Optimización de Medios (WebP/AVIF), Seguridad y Core Web Vitals*.
* 🛠️ [**skills.md**](skills.md): Recetario técnico con snippets de código PHP para JSON-LD, JavaScript para Wake Lock API, CSS `@media print` y scripts WP-CLI.

---

## 📂 Estructura del Repositorio

```text
mi-sitio/
├── docs/                       # Documentación detallada por plataforma
│   ├── README_LINUX.md         # Guía para CachyOS / Arch / Ubuntu / Fedora
│   ├── README_MACOS.md         # Guía para macOS Apple Silicon e Intel
│   └── README_WINDOWS.md       # Guía para Windows 10/11 (WSL2, LocalWP, PowerShell)
├── AGENTS.md                   # Protocolo de gobernanza y roles técnicos
├── tasks.md                    # Plan de trabajo y control de fases
├── skills.md                   # Biblioteca de snippets de código y procedimientos
├── wp-config.php               # Archivo de configuración de WordPress y Base de Datos
├── .gitignore                  # Exclusión de volcados SQL, backups y ficheros temporales
├── wp-content/
│   ├── plugins/
│   │   ├── recetas-core/       # Plugin principal de lógica (CPT, taxonomías, Schema)
│   │   └── akismet/            # Protección anti-spam
│   ├── themes/
│   │   ├── twentytwentyfive/   # Tema FSE base oficial
│   │   └── recetas-theme/      # Tema hijo / personalizaciones de plantillas
│   └── uploads/                # Archivos multimedia subidos (WebP, JPG)
├── wp-admin/                   # Núcleo de administración de WordPress
└── wp-includes/                # Librerías y funciones del núcleo
```

---

## 🛠️ Comandos Esenciales de Gestión con WP-CLI

WP-CLI permite gestionar el 100% de WordPress sin tocar el panel web:

### 🍝 Recetas y Taxonomías
```bash
# Crear una receta de prueba completa con tiempos y metacampos
wp post create --post_type=receta \
  --post_title="Paella Valenciana Tradicional" \
  --post_status=publish \
  --meta_input='{"_receta_tiempo_preparacion":"PT30M","_receta_tiempo_coccion":"PT45M","_receta_porciones":4,"_receta_dificultad":"Media"}'

# Añadir términos a las taxonomías gastronómicas
wp term create tipo_cocina "Mediterránea"
wp term create tipo_cocina "Mexicana"
wp term create dieta "Vegetariana"
wp term create dieta "Sin Gluten"
```

### 🧩 Plugins y Temas
```bash
# Ver estado de plugins
wp plugin list

# Activar o desactivar plugins
wp plugin activate recetas-core
wp plugin deactivate hello

# Ver temas instalados
wp theme list
```

### 💾 Base de Datos y Backups
```bash
# Exportar respaldo rápido de base de datos
wp db export backup_recetas.sql

# Importar respaldo
wp db import backup_recetas.sql

# Optimizar tablas de MariaDB
wp db optimize

# Consola interactiva MySQL
wp db cli
```

### 🔄 Migraciones y Reemplazo de URLs
```bash
# Actualizar URL si cambias de puerto o dominio
wp search-replace 'http://localhost:8080' 'http://localhost:3000'
```

---

## 🛡️ Depuración y Buenas Prácticas

### Activar Modo de Depuración (`WP_DEBUG`)
Durante el desarrollo de plugins o bloques, habilita el registro de errores en [wp-config.php](wp-config.php):

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );      // Guarda los errores en wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // No muestra errores visuales a los visitantes
@ini_set( 'display_errors', 0 );
```

---

## ❓ Preguntas Frecuentes y Solución de Problemas

### 1. `PHP Warning: Unable to load dynamic library 'gd'`
* **Causa:** En Arch Linux o CachyOS falta el paquete oficial de la librería gráfica.
* **Solución:** Ejecuta `sudo pacman -S php-gd`.

### 2. `Allowed memory size exhausted`
* **Solución:** Incrementa el límite de memoria en tu `php.ini`:
  ```ini
  memory_limit = 512M
  ```

### 3. `Port 8080 is already in use`
* **Solución:** Inicia el servidor en un puerto alternativo:
  ```bash
  wp server --port=8081
  ```

---

## 📄 Licencia

Este proyecto se distribuye bajo la licencia **GPLv2 o superior**, en consonancia con la licencia del núcleo de WordPress.
