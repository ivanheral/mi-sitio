# 🐧 Guía de Instalación, Configuración y Uso en Linux

Esta guía detalla paso a paso cómo montar, configurar y operar el entorno de desarrollo del **Blog de Recetas de Cocina (WordPress)** en distribuciones Linux (**CachyOS / Arch Linux**, **Ubuntu / Debian** y **Fedora**).

---

## 📋 Índice
1. [Requisitos Previos del Sistema](#1-requisitos-previos-del-sistema)
2. [Instalación de Dependencias por Distribución](#2-instalación-de-dependencias-por-distribución)
3. [Configuración del Motor PHP](#3-configuración-del-motor-php)
4. [Inicialización y Configuración de MariaDB](#4-inicialización-y-configuración-de-mariadb)
5. [Instalación y Configuración de WordPress](#5-instalación-y-configuración-de-wordpress)
6. [Arranque y Flujo de Trabajo Diario](#6-arranque-y-flujo-de-trabajo-diario)
7. [Permisos de Archivos y Directorios](#7-permisos-de-archivos-y-directorios)
8. [Comandos Esenciales de Gestión (WP-CLI)](#8-comandos-esenciales-de-gestión-wp-cli)
9. [Solución de Problemas Frecuentes](#9-solución-de-problemas-frecuentes)

---

## 1. Requisitos Previos del Sistema

* **PHP:** Versión 8.1, 8.2 o superior (con extensiones `mysqli`, `pdo_mysql`, `gd`, `curl`, `mbstring`, `xml`, `zip`).
* **Base de Datos:** MariaDB 10.5+ o MySQL 8.0+.
* **Gestor CLI:** WP-CLI 2.x.
* **Control de versiones:** Git.

---

## 2. Instalación de Dependencias por Distribución

Selecciona tu distribución y ejecuta los comandos correspondientes en tu terminal:

### A. CachyOS / Arch Linux / Manjaro
```bash
# Actualizar repositorios e instalar paquetes base
sudo pacman -Syu
sudo pacman -S php php-gd php-curl mariadb wp-cli git

# Verificar versiones instaladas
php -v
mariadb --version
wp --version
```

> [!IMPORTANT]
> En Arch y CachyOS, la extensión gráfica **GD** viene en un paquete separado llamado `php-gd`. Si no se instala, PHP arrojará la advertencia `Unable to load dynamic library 'gd'` y WordPress no podrá procesar miniaturas ni convertir imágenes a WebP.

---

### B. Ubuntu / Debian / Linux Mint
```bash
# Actualizar lista de paquetes
sudo apt update && sudo apt upgrade -y

# Instalar PHP, módulos requeridos y MariaDB
sudo apt install -y php php-cli php-mysql php-gd php-curl php-mbstring php-xml php-zip mariadb-server curl git

# Instalar WP-CLI globalmente
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp

# Comprobar instalación
wp --info
```

---

### C. Fedora / RHEL
```bash
sudo dnf check-update
sudo dnf install -y php php-cli php-mysqlnd php-gd php-curl php-mbstring php-xml php-zip mariadb-server curl git

# Instalar WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

---

## 3. Configuración del Motor PHP

WordPress requiere límites de memoria adecuados para compresión de imágenes, plugins de recetas y operaciones de base de datos.

### 1. Localizar el archivo `php.ini`
Ejecuta:
```bash
php --ini | grep "Loaded Configuration File"
```
* **En Arch / CachyOS:** `/etc/php/php.ini`
* **En Ubuntu / Debian:** `/etc/php/8.x/cli/php.ini` (y `/etc/php/8.x/apache2/php.ini` si usas Apache)
* **En Fedora:** `/etc/php.ini`

### 2. Parámetros recomendados
Edita el archivo correspondiente con permisos de administrador (`sudo nano /etc/php/php.ini` o similar):

```ini
; Límites de memoria y tiempo de ejecución
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 64M
post_max_size = 64M

; Extensiones indispensables (en Arch Linux quitar el punto y coma ';' inicial)
extension=mysqli
extension=pdo_mysql
extension=gd
extension=curl
```

*(En Ubuntu/Debian las extensiones se habilitan automáticamente tras instalar los paquetes `php-*`).*

---

## 4. Inicialización y Configuración de MariaDB

### Paso 1: Inicializar el almacén de datos (Solo requerido en Arch/CachyOS si es la primera vez)
```bash
sudo mariadb-install-db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
```

### Paso 2: Iniciar y habilitar el servicio del sistema
```bash
# Iniciar MariaDB ahora y configurarlo para que arranque con el sistema
sudo systemctl enable --now mariadb

# Comprobar que está en estado 'active (running)'
systemctl status mariadb
```

### Paso 3: Crear la base de datos y el usuario del proyecto
Accede a la consola de MariaDB (en Linux, el usuario `root` de MariaDB entra por defecto mediante socket Unix con `sudo`):

```bash
sudo mariadb
```

Dentro de la consola interactiva de MariaDB, copia y pega las siguientes sentencias SQL:

```sql
-- 1. Crear base de datos en UTF-8 multilingüe
CREATE DATABASE IF NOT EXISTS misitio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Crear usuario 'admin' con la contraseña del proyecto
CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'secreto123';

-- 3. Otorgar permisos completos sobre la base de datos
GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';

-- 4. Aplicar cambios y salir
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Instalación y Configuración de WordPress

### Paso 1: Clonar o situarse en el repositorio
```bash
cd ~/Escritorio/mi-sitio
```

### Paso 2: Crear el archivo de configuración (`wp-config.php`)
Si estás clonando el repositorio por primera vez y no dispones de `wp-config.php`, créalo automáticamente con WP-CLI:

```bash
wp config create \
  --dbname=misitio_db \
  --dbuser=admin \
  --dbpass=secreto123 \
  --dbhost=localhost \
  --locale=es_ES
```

### Paso 3: Instalar WordPress en la Base de Datos
Ejecuta la instalación inicial definiendo el usuario administrador del panel:

```bash
wp core install \
  --url="http://localhost:8080" \
  --title="Blog de Recetas de Cocina" \
  --admin_user="dev" \
  --admin_password="PasswordSegura2026!" \
  --admin_email="dev@misitio.local" \
  --skip-email
```

### Paso 4: Descargar e instalar idioma español oficial
```bash
wp language core install es_ES --activate
```

---

## 6. Arranque y Flujo de Trabajo Diario

### Arrancar el servidor de desarrollo
WordPress incluye un servidor web integrado impulsado por PHP y WP-CLI:

```bash
cd ~/Escritorio/mi-sitio
wp server --port=8080
```

* **Sitio público:** [http://localhost:8080](http://localhost:8080)
* **Panel de administración:** [http://localhost:8080/wp-admin](http://localhost:8080/wp-admin)
  * **Usuario:** `dev`
  * **Contraseña:** `PasswordSegura2026!`

### Detener el servidor
En la terminal donde esté corriendo, presiona **`Ctrl + C`**.

---

## 7. Permisos de Archivos y Directorios

En Linux, los problemas al subir imágenes o actualizar plugins suelen deberse a permisos de usuario. Asegúrate de que tu usuario es el dueño de la carpeta del proyecto:

```bash
# Asignar a tu usuario actual como propietario de todo el proyecto
sudo chown -R $USER:$USER ~/Escritorio/mi-sitio

# Permisos estándar: 755 para carpetas y 644 para archivos
find ~/Escritorio/mi-sitio/ -type d -exec chmod 755 {} \;
find ~/Escritorio/mi-sitio/ -type f -exec chmod 644 {} \;
```

---

## 8. Comandos Esenciales de Gestión (WP-CLI)

### Base de Datos y Respaldos
```bash
# Exportar base de datos antes de cambios mayores
wp db export backup_$(date +%Y%m%d).sql

# Restaurar base de datos
wp db import backup_20261027.sql

# Abrir consola SQL directa
wp db cli
```

### Creación de Contenido para Recetas
```bash
# Crear una receta de prueba con metadatos
wp post create --post_type=receta \
  --post_title="Risotto de Setas Silvestres" \
  --post_status=publish \
  --meta_input='{"_receta_tiempo_preparacion":"PT15M","_receta_tiempo_coccion":"PT25M","_receta_porciones":4,"_receta_dificultad":"Media"}'
```

### Regeneración de Medios
Si añades nuevos tamaños de imagen o formatos WebP:
```bash
wp media regenerate --yes
```

---

## 9. Solución de Problemas Frecuentes

### 1. `PHP Warning: Unable to load dynamic library 'gd'`
* **Causa:** Paquete gráfico de PHP ausente en Arch/CachyOS.
* **Solución:**
  ```bash
  sudo pacman -S php-gd
  ```

### 2. `Error establishing a database connection`
* **Causa:** El servicio MariaDB no está ejecutándose.
* **Solución:**
  ```bash
  sudo systemctl start mariadb
  ```
  Verifica con `systemctl status mariadb`.

### 3. `Port 8080 is already in use`
* **Causa:** Otro proceso está utilizando el puerto 8080.
* **Solución:** Descubre qué proceso lo ocupa o usa otro puerto:
  ```bash
  # Ver qué proceso ocupa el puerto
  ss -tulpn | grep 8080
  
  # O simplemente arranca en el puerto 8081
  wp server --port=8081
  ```

### 4. Modo Depuración (`WP_DEBUG`)
Para ver errores PHP en pantalla y guardarlos en `wp-content/debug.log`, añade a tu [wp-config.php](file:///home/ivan/Escritorio/mi-sitio/wp-config.php):
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );
```
