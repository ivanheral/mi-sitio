# 🍏 Guía de Instalación, Configuración y Uso en macOS

Esta guía detalla paso a paso cómo montar, configurar y operar el entorno de desarrollo del **Blog de Recetas de Cocina (WordPress)** en **macOS** (compatible con procesadores Apple Silicon **M1/M2/M3/M4** e **Intel**).

---

## 📋 Índice
1. [Requisitos Previos y Herramientas Base](#1-requisitos-previos-y-herramientas-base)
2. [Instalación del Stack con Homebrew](#2-instalación-del-stack-con-homebrew)
3. [Configuración del Motor PHP](#3-configuración-del-motor-php)
4. [Configuración e Inicio de MariaDB](#4-configuración-e-inicio-de-mariadb)
5. [Instalación y Configuración de WordPress](#5-instalación-y-configuración-de-wordpress)
6. [Arranque y Flujo de Trabajo Diario](#6-arranque-y-flujo-de-trabajo-diario)
7. [Alternativa Visual: LocalWP](#7-alternativa-visual-localwp)
8. [Comandos Esenciales de Gestión (WP-CLI)](#8-comandos-esenciales-de-gestión-wp-cli)
9. [Solución de Problemas Frecuentes](#9-solución-de-problemas-frecuentes)

---

## 1. Requisitos Previos y Herramientas Base

### Paso 1: Instalar Xcode Command Line Tools
Abre la aplicación **Terminal** de macOS y ejecuta:
```bash
xcode-select --install
```
*(Si ya lo tienes instalado, el sistema indicará que ya está presente).*

### Paso 2: Instalar Homebrew
Homebrew es el gestor de paquetes estándar de facto para macOS. Si aún no lo tienes instalado:
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

> [!NOTE]
> En Mac con **Apple Silicon (M1/M2/M3/M4)**, asegúrate de añadir Homebrew a tu `PATH` si el instalador te lo solicita:
> ```bash
> echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
> eval "$(/opt/homebrew/bin/brew shellenv)"
> ```

---

## 2. Instalación del Stack con Homebrew

Ejecuta el siguiente comando para instalar PHP, MariaDB, WP-CLI y Git:

```bash
brew install php mariadb wp-cli git

# Comprobar versiones instaladas
php -v
mariadb --version
wp --version
```

*(En macOS, las fórmulas de Homebrew para PHP ya incluyen compilados los módulos `mysqli`, `pdo_mysql`, `curl`, `mbstring` y `gd` por defecto).*

---

## 3. Configuración del Motor PHP

### 1. Localizar el archivo de configuración `php.ini`
En macOS la ubicación depende de la arquitectura de tu procesador:
* **Apple Silicon (M1/M2/M3/M4):** `/opt/homebrew/etc/php/8.x/php.ini`
* **Intel (x86_64):** `/usr/local/etc/php/8.x/php.ini`

Puedes obtener la ruta exacta automáticamente con:
```bash
PHP_INI=$(php -r "echo php_ini_loaded_file();")
echo "Tu archivo php.ini está en: $PHP_INI"
```

### 2. Aumentar los límites de memoria
Edita el archivo `php.ini` o ejecuta estos comandos para actualizar los límites automáticamente:

```bash
sed -i '' 's/^memory_limit = .*/memory_limit = 512M/' "$PHP_INI"
sed -i '' 's/^upload_max_filesize = .*/upload_max_filesize = 64M/' "$PHP_INI"
sed -i '' 's/^post_max_size = .*/post_max_size = 64M/' "$PHP_INI"
sed -i '' 's/^max_execution_time = .*/max_execution_time = 300/' "$PHP_INI"
```

---

## 4. Configuración e Inicio de MariaDB

### Paso 1: Iniciar MariaDB como servicio en segundo plano
Homebrew gestiona demonios de fondo de manera limpia con `brew services`:

```bash
# Iniciar MariaDB
brew services start mariadb

# Comprobar que está en estado 'started'
brew services list
```

### Paso 2: Crear la base de datos y usuario del proyecto
Accede a MariaDB (por defecto en Homebrew el usuario `root` no tiene contraseña):

```bash
mariadb -u root
```

Dentro de la consola SQL, ejecuta:

```sql
-- 1. Crear base de datos en UTF-8 multilingüe
CREATE DATABASE IF NOT EXISTS misitio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Crear usuario 'admin' con la contraseña del proyecto
CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'secreto123';

-- 3. Otorgar permisos completos
GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';

-- 4. Aplicar cambios y salir
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Instalación y Configuración de WordPress

### Paso 1: Ubicarse en el directorio del proyecto
```bash
cd ~/Desktop/mi-sitio
```

### Paso 2: Crear el archivo de configuración (`wp-config.php`)
Si configuras el entorno desde cero:

```bash
wp config create \
  --dbname=misitio_db \
  --dbuser=admin \
  --dbpass=secreto123 \
  --dbhost=127.0.0.1 \
  --locale=es_ES
```

> [!TIP]
> En macOS es recomendable usar `--dbhost=127.0.0.1` en vez de `localhost` si experimentas conflictos de resolución con sockets Unix de MySQL.

### Paso 3: Instalar WordPress en la base de datos
```bash
wp core install \
  --url="http://localhost:8080" \
  --title="Blog de Recetas de Cocina" \
  --admin_user="dev" \
  --admin_password="PasswordSegura2026!" \
  --admin_email="dev@misitio.local" \
  --skip-email
```

### Paso 4: Instalar y activar el idioma español
```bash
wp language core install es_ES --activate
```

---

## 6. Arranque y Flujo de Trabajo Diario

### Iniciar el servidor local
```bash
cd ~/Desktop/mi-sitio
wp server --port=8080
```

* **Sitio público:** [http://localhost:8080](http://localhost:8080)
* **Panel de administración:** [http://localhost:8080/wp-admin](http://localhost:8080/wp-admin)
  * **Usuario:** `dev`
  * **Contraseña:** `PasswordSegura2026!`

### Detener el servidor
Presiona **`Ctrl + C`** en la terminal donde corre el proceso.

---

## 7. Alternativa Visual: LocalWP

Si prefieres una interfaz gráfica con soporte para certificados SSL locales (HTTPS automático) y compartición de enlaces en vivo (*Live Links*), puedes instalar **LocalWP**:

```bash
brew install --cask local
```
Abre la aplicación **Local** desde tu carpeta de Aplicaciones, crea un nuevo sitio y selecciona la carpeta del proyecto.

---

## 8. Comandos Esenciales de Gestión (WP-CLI)

Ejecuta WP-CLI desde el directorio del proyecto. Puedes consultar los temas instalados y activar el tema predeterminado de WordPress:

```bash
cd ~/Desktop/mi-sitio
wp theme list
wp theme activate twentytwentyfive
```

Para crear una página en borrador y publicarla cuando esté revisada:

```bash
wp post create --post_type=page --post_title="Acerca de este sitio" --post_status=draft
# Sustituye 42 por el ID que devolvió el comando anterior
wp post update 42 --post_status=publish
```

La publicación de una entrada o página no hace que el sitio esté disponible en Internet. Para cambiar el tema, publicar contenido y desplegar el sitio en un alojamiento, consulta la [guía multiplataforma de WP-CLI](../GUIA_MULTIPLATAFORMA.md). El servidor `wp server` descrito en esta guía es solo de desarrollo local.

### Copias de Seguridad y Base de Datos
```bash
# Exportar respaldo
wp db export backup_$(date +%Y%m%d).sql

# Restaurar respaldo
wp db import backup_20261027.sql
```

### Contenido de Prueba de Recetas
```bash
# Crear receta con campos personalizados
wp post create --post_type=receta \
  --post_title="Tarta de Queso La Viña" \
  --post_status=publish \
  --meta_input='{"_receta_tiempo_preparacion":"PT20M","_receta_tiempo_coccion":"PT50M","_receta_porciones":8,"_receta_dificultad":"Fácil"}'
```

---

## 9. Solución de Problemas Frecuentes

### 1. `Can't connect to local MySQL server through socket '/tmp/mysql.sock'`
* **Causa:** MariaDB no está arrancado o WordPress intenta conectarse por socket en una ruta distinta.
* **Solución:**
  1. Asegúrate de que el servicio está activo: `brew services start mariadb`.
  2. En [wp-config.php](file:///home/ivan/Escritorio/mi-sitio/wp-config.php), cambia `define('DB_HOST', 'localhost');` por `define('DB_HOST', '127.0.0.1');` para obligar la conexión por TCP/IP.

### 2. `Port 8080 is already in use`
* **Causa:** Otra aplicación tiene ocupado el puerto 8080.
* **Solución:**
  ```bash
  # Ver qué proceso lo ocupa
  lsof -i :8080
  
  # O arrancar en un puerto alternativo
  wp server --port=8081
  ```

### 3. Error de permisos en uploads al subir imágenes
```bash
sudo chown -R $(whoami):staff ~/Desktop/mi-sitio/wp-content/uploads
chmod -R 755 ~/Desktop/mi-sitio/wp-content/uploads
```
