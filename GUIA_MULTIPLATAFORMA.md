# 🌐 Guía Multiplataforma: WordPress Local (CachyOS / Arch, macOS y Windows)

Esta guía explica paso a paso cómo montar, configurar y gestionar un entorno de desarrollo **WordPress** completo y moderno según el sistema operativo que utilices: **CachyOS / Arch Linux**, **macOS** o **Windows**.

---

## 🧭 Resumen de Estrategias por Sistema

| Característica | 🐧 CachyOS / Arch Linux | 🍏 macOS (Apple Silicon / Intel) | 🪟 Windows (10 / 11) |
| :--- | :--- | :--- | :--- |
| **Gestor de paquetes** | `pacman` / `paru` / `yay` | Homebrew (`brew`) | `winget` / Scoop / WSL2 |
| **Método CLI Recomendado** | Nativo (`php` + `mariadb` + `wp-cli`) | Nativo con Homebrew | **WSL2** (Ubuntu/Arch) o Nativo |
| **Método GUI (1 clic)** | `localwp` (AUR) | `local` (Homebrew Cask) | `Local.Local` (winget) |
| **Ruta archivo `php.ini`** | `/etc/php/php.ini` | `/opt/homebrew/etc/php/8.x/php.ini` | `C:\tools\php\php.ini` o en WSL2 |

---

## 🐧 1. CachyOS / Arch Linux

### Método Terminal (Nativo)

#### Paso 1: Instalar dependencias
```bash
sudo pacman -S php mariadb wp-cli
```

#### Paso 2: Configurar PHP (`/etc/php/php.ini`)
En Arch/CachyOS las extensiones y límites vienen desactivados por defecto. Habilítalos:
```bash
# Aumentar límite de memoria a 512M
sudo sed -i 's/^memory_limit = 128M/memory_limit = 512M/' /etc/php/php.ini

# Activar extensiones para MySQL y gráficos
sudo sed -i 's/^;extension=mysqli/extension=mysqli/' /etc/php/php.ini
sudo sed -i 's/^;extension=pdo_mysql/extension=pdo_mysql/' /etc/php/php.ini
sudo sed -i 's/^;extension=gd/extension=gd/' /etc/php/php.ini
```

#### Paso 3: Inicializar y arrancar MariaDB
```bash
# Inicializar base de datos por primera vez
sudo mariadb-install-db --user=mysql --basedir=/usr --datadir=/var/lib/mysql

# Iniciar el servicio y habilitarlo al arranque
sudo systemctl enable --now mariadb

# Crear la base de datos y usuario para WordPress
sudo mariadb -e "CREATE DATABASE misitio_db; CREATE USER 'admin'@'localhost' IDENTIFIED BY 'secreto123'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';"
```

#### Paso 4: Descargar y arrancar WordPress
```bash
mkdir -p ~/Escritorio/mi-sitio && cd ~/Escritorio/mi-sitio
wp core download
wp config create --dbname=misitio_db --dbuser=admin --dbpass=secreto123
wp server --port=8080
```

> **Método Alternativo Gráfico en CachyOS:**  
> Instala LocalWP desde AUR: `paru -S localwp`

---

## 🍏 2. macOS (Apple Silicon M1/M2/M3/M4 e Intel)

En macOS, el estándar absoluto para instalar herramientas de desarrollo es [Homebrew](https://brew.sh/).

### Método Terminal (Homebrew)

#### Paso 1: Instalar dependencias
```bash
brew install php mariadb wp-cli
```

#### Paso 2: Configurar PHP
En macOS con Apple Silicon, la ruta de configuración es `/opt/homebrew/etc/php/8.x/php.ini` (en Intel es `/usr/local/etc/php/8.x/php.ini`):
```bash
PHP_INI=$(php -r "echo php_ini_loaded_file();")
sed -i '' 's/^memory_limit = .*/memory_limit = 512M/' "$PHP_INI"
```
*(En macOS Homebrew ya viene con `mysqli`, `pdo_mysql` y `gd` compilados y activos por defecto).*

#### Paso 3: Iniciar MariaDB y crear la base de datos
Homebrew gestiona los servicios en segundo plano con `brew services`:
```bash
# Iniciar MariaDB como servicio del sistema
brew services start mariadb

# Crear base de datos y usuario
mariadb -u root -e "CREATE DATABASE misitio_db; CREATE USER 'admin'@'localhost' IDENTIFIED BY 'secreto123'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';"
```

#### Paso 4: Descargar y arrancar WordPress
```bash
mkdir -p ~/Desktop/mi-sitio && cd ~/Desktop/mi-sitio
wp core download
wp config create --dbname=misitio_db --dbuser=admin --dbpass=secreto123
wp server --port=8080
```

> **Método Alternativo Gráfico en macOS:**  
> Instala LocalWP con un solo comando en terminal:  
> ```bash
> brew install --cask local
> ```

---

## 🪟 3. Windows (10 y 11)

En Windows existen dos enfoques: el entorno **Nativo / GUI** y el entorno **WSL2 (Subsistema de Linux)**.

---

### Opción A: Vía WSL2 (La más recomendada para desarrolladores en Windows)
WSL2 ejecuta un kernel de Linux real dentro de Windows con rendimiento nativo.

1. Abre **PowerShell como Administrador** e instala WSL:
   ```powershell
   wsl --install
   ```
2. Reinicia el ordenador si te lo pide y abre la terminal de **Ubuntu**.
3. Sigue exactamente los mismos pasos de Linux:
   ```bash
   sudo apt update && sudo apt install -y php php-mysqli php-gd mariadb-server curl
   # Instalar wp-cli
   curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
   chmod +x wp-cli.phar && sudo mv wp-cli.phar /usr/local/bin/wp
   
   # Iniciar MariaDB
   sudo service mariadb start
   sudo mariadb -e "CREATE DATABASE misitio_db; CREATE USER 'admin'@'localhost' IDENTIFIED BY 'secreto123'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';"
   
   # Crear sitio y correr servidor
   mkdir ~/mi-sitio && cd ~/mi-sitio
   wp core download
   wp config create --dbname=misitio_db --dbuser=admin --dbpass=secreto123
   wp server --port=8080
   ```
4. Abre `http://localhost:8080` en tu navegador habitual de Windows (Edge, Chrome, Firefox).

---

### Opción B: LocalWP Nativo en Windows (La más sencilla y visual)
Configurar PHP, extensiones y MariaDB como servicios de Windows manualmente suele dar problemas de rutas (slashes `\`) y permisos. Por ello, en Windows la recomendación oficial es **LocalWP**:

1. Abre **PowerShell** o CMD e instala LocalWP mediante `winget`:
   ```powershell
   winget install Local.Local
   ```
2. Abre la aplicación **Local** desde el menú Inicio.
3. Haz clic en **`Create a new site`**, elige el nombre de tu web y en **Advanced options** puedes ubicarlo directamente en tu Escritorio (`C:\Users\TuUsuario\Desktop\mi-sitio`).
4. LocalWP gestiona los procesos de PHP y MySQL en segundo plano y te da acceso con 1 clic.

---

## ⚡ Comandos Universales WP-CLI (Comunes a todos los sistemas)

Una vez que el sitio está descargado y configurado con la base de datos (sea en Linux, Mac o Windows/WSL), estos comandos funcionan exactamente igual en todos los sistemas operativos:

```bash
# 1. Configurar el idioma español
wp language core install es_ES --activate

# 2. Instalar el sitio (crear usuario admin y título)
wp core install --url=http://localhost:8080 --title="Mi Proyecto" --admin_user=admin --admin_password="Password123!" --admin_email=tu@correo.com

# 3. Instalar y activar plugins esenciales
wp plugin install woocommerce --activate
wp plugin install elementor --activate

# 4. Instalar y activar un tema
wp theme install astra --activate

# 5. Exportar copia de seguridad de la base de datos
wp db export backup.sql

# 6. Importar base de datos
wp db import backup.sql

# 7. Cambiar URLs (por ejemplo al subir a producción)
wp search-replace 'http://localhost:8080' 'https://midominio.com'
```

---

## 🛡️ Solución de Problemas Frecuentes

### 1. `Allowed memory size of X bytes exhausted`
* **Causa:** PHP tiene un límite de memoria insuficiente al descomprimir archivos grandes.
* **Solución:** Ajustar `memory_limit = 512M` en el archivo `php.ini` correspondiente de tu sistema.

### 2. `Call to undefined function mysqli_init()`
* **Causa:** La extensión `mysqli` no está habilitada en `php.ini`.
* **Solución:** Descomentar `extension=mysqli` y reiniciar el servidor/servicio PHP.

### 3. `Port 8080 already in use`
* **Causa:** Otro proceso está ocupando el puerto 8080.
* **Solución:** Lanza el servidor en otro puerto alternativo:
  ```bash
  wp server --port=8081
  # o
  wp server --port=3000
  ```
