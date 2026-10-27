# 🪟 Guía de Instalación, Configuración y Uso en Windows

Esta guía detalla paso a paso cómo montar, configurar y operar el entorno de desarrollo del **Blog de Recetas de Cocina (WordPress)** en **Windows 10** y **Windows 11**.

En Windows existen dos métodos principales para trabajar:
* **Método 1 (Recomendado para Desarrolladores): WSL2 (Subsistema de Linux)** — Idéntico a Linux, máximo rendimiento y compatibilidad nativa con WP-CLI.
* **Método 2 (Recomendado para Diseñadores / Entorno Visual): LocalWP con Winget** — Instalación gráfica con 1 solo clic y gestión automática de puertos y SSL.
* **Método 3: Stack Nativo de Windows (PowerShell + Scoop / Winget)**.

---

## 📋 Índice
1. [Método 1: Desarrollo con WSL2 (Ubuntu / Debian)](#método-1-desarrollo-con-wsl2-la-opción-recomendada)
2. [Método 2: Entorno Gráfico con LocalWP (1 Clic)](#método-2-entorno-gráfico-con-localwp-1-clic)
3. [Método 3: Stack Nativo CLI en Windows (PowerShell)](#método-3-stack-nativo-cli-en-windows-powershell)
4. [Ajuste de Saltos de Línea en Git (CRLF vs LF)](#ajuste-de-saltos-de-línea-en-git-crlf-vs-lf)
5. [Comandos Esenciales de Gestión (WP-CLI)](#comandos-esenciales-de-gestión-wp-cli)
6. [Solución de Problemas Frecuentes](#solución-de-problemas-frecuentes)

---

## Método 1: Desarrollo con WSL2 (La Opción Recomendada)

WSL2 ejecuta un kernel real de Linux integrado en Windows. Las peticiones a `http://localhost:8080` se comunican de forma automática y transparente con tu navegador de Windows (Chrome, Edge, Firefox).

### Paso 1: Instalar WSL2
1. Haz clic derecho en el botón de Inicio de Windows y selecciona **Terminal (Administrador)** o **PowerShell (Administrador)**.
2. Ejecuta:
   ```powershell
   wsl --install -d Ubuntu
   ```
3. Reinicia tu equipo cuando finalice la instalación. Al reiniciar, se abrirá la consola de **Ubuntu** solicitándote que crees un usuario y contraseña.

> [!TIP]
> **Rendimiento de archivos en WSL2:**  
> Clona o copia el proyecto dentro del sistema de archivos de Linux (por ejemplo, en `~/mi-sitio` dentro de WSL), y **no** en `/mnt/c/Users/...`. El acceso a disco dentro de Linux es hasta 10 veces más rápido. Puedes explorar tus archivos desde el Explorador de Windows escribiendo en la barra de direcciones:  
> `\\wsl$\Ubuntu\home\tu_usuario\mi-sitio`

### Paso 2: Instalar PHP, MariaDB y WP-CLI en Ubuntu (WSL)
Abre la aplicación **Ubuntu** desde el menú Inicio de Windows y ejecuta:

```bash
# Actualizar lista de paquetes
sudo apt update && sudo apt upgrade -y

# Instalar PHP y extensiones
sudo apt install -y php php-cli php-mysql php-gd php-curl php-mbstring php-xml php-zip mariadb-server curl git

# Instalar WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

### Paso 3: Configurar PHP (`php.ini`) en WSL
Aumenta el límite de memoria en el archivo de configuración:
```bash
sudo sed -i 's/^memory_limit = .*/memory_limit = 512M/' /etc/php/*/cli/php.ini
sudo sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/*/cli/php.ini
sudo sed -i 's/^post_max_size = .*/post_max_size = 64M/' /etc/php/*/cli/php.ini
```

### Paso 4: Iniciar MariaDB y crear la Base de Datos
En WSL2, inicia el servicio de MariaDB:
```bash
sudo service mariadb start
```
*(Si tu WSL2 tiene systemd habilitado, también puedes usar `sudo systemctl start mariadb`).*

Accede a la consola de base de datos:
```bash
sudo mariadb
```
Ejecuta las instrucciones SQL del proyecto:
```sql
CREATE DATABASE IF NOT EXISTS misitio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'secreto123';
GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Paso 5: Puesta en Marcha de WordPress
```bash
# Navegar a la carpeta del proyecto
cd ~/mi-sitio

# Crear wp-config.php si no existe
wp config create --dbname=misitio_db --dbuser=admin --dbpass=secreto123 --locale=es_ES

# Instalar WordPress
wp core install \
  --url="http://localhost:8080" \
  --title="Blog de Recetas de Cocina" \
  --admin_user="dev" \
  --admin_password="PasswordSegura2026!" \
  --admin_email="dev@misitio.local" \
  --skip-email

# Descargar idioma español
wp language core install es_ES --activate
```

### Paso 6: Arrancar el servidor
```bash
wp server --port=8080
```
Abre tu navegador en Windows y entra en:
* **Sitio:** [http://localhost:8080](http://localhost:8080)
* **Admin:** [http://localhost:8080/wp-admin](http://localhost:8080/wp-admin)

---

## Método 2: Entorno Gráfico con LocalWP (1 Clic)

Si no quieres usar terminales de Linux ni configurar servicios, **LocalWP** es la herramienta estándar para Windows.

### Paso 1: Instalar mediante Windows Package Manager (winget)
Abre **PowerShell** y ejecuta:
```powershell
winget install Local.Local
```

### Paso 2: Crear el sitio
1. Abre **Local** desde el menú Inicio.
2. Haz clic en **`Create a new site`** -> **`Create a new site`**.
3. En el nombre, escribe `mi-sitio`.
4. En **Advanced Options**, selecciona la carpeta donde tienes el proyecto (por ejemplo `C:\Users\TuUsuario\Desktop\mi-sitio`).
5. Elige la configuración **Preferred** (PHP 8.2+, MySQL 8/MariaDB).
6. Configura tu usuario y contraseña de WordPress.
7. Haz clic en **`Open site`** o **`WP Admin`**.

---

## Método 3: Stack Nativo CLI en Windows (PowerShell)

Para desarrolladores que desean ejecutar PHP y MariaDB directamente sobre el sistema de archivos de Windows sin máquinas virtuales ni WSL.

### Paso 1: Instalar dependencias con Scoop
[Scoop](https://scoop.sh/) es un gestor de paquetes por línea de comandos para Windows:

```powershell
# Instalar Scoop si no lo tienes
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
irm get.scoop.sh | iex

# Instalar PHP, MariaDB y WP-CLI
scoop install php mariadb wp-cli git
```

### Paso 2: Configurar `php.ini` en Windows
1. Abre `C:\Users\TuUsuario\scoop\apps\php\current\php.ini`.
2. Asegúrate de habilitar las extensiones eliminando el punto y coma `;` inicial:
   ```ini
   extension_dir = "ext"
   extension=mysqli
   extension=pdo_mysql
   extension=gd
   extension=curl
   extension=mbstring
   memory_limit = 512M
   ```

### Paso 3: Iniciar MariaDB en Windows
```powershell
# Iniciar el demonio de MariaDB en segundo plano
mysqld --console
```
En otra pestaña de PowerShell:
```powershell
mysql -u root -e "CREATE DATABASE misitio_db; CREATE USER 'admin'@'localhost' IDENTIFIED BY 'secreto123'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'admin'@'localhost';"
```

### Paso 4: Arrancar WordPress
```powershell
cd C:\Users\TuUsuario\Desktop\mi-sitio
wp server --port=8080
```

---

## Ajuste de Saltos de Línea en Git (CRLF vs LF)

Windows utiliza por defecto retornos de carro CRLF (`\r\n`), mientras que Linux y macOS usan LF (`\n`). Para evitar conflictos en scripts PHP y Git diffs en proyectos colaborativos, ejecuta en PowerShell:

```powershell
git config --global core.autocrlf input
```

---

## Comandos Esenciales de Gestión (WP-CLI)

Todos estos comandos funcionan tanto en la terminal de **Ubuntu (WSL2)** como en **PowerShell** si tienes instalado WP-CLI:

```bash
# Ver estado del sitio
wp core is-installed

# Exportar copia de seguridad de la base de datos
wp db export copia_seguridad.sql

# Crear receta gastronómica de prueba
wp post create --post_type=receta \
  --post_title="Gazpacho Andaluz Tradicional" \
  --post_status=publish \
  --meta_input='{"_receta_tiempo_preparacion":"PT15M","_receta_tiempo_coccion":"PT0M","_receta_porciones":6,"_receta_dificultad":"Fácil"}'
```

---

## Solución de Problemas Frecuentes

### 1. `Can't connect to MySQL server` en WSL2
* **Causa:** El servicio de MariaDB no está arrancado dentro del entorno WSL.
* **Solución:**
  ```bash
  sudo service mariadb start
  ```

### 2. Conflicto con el Firewall de Windows
* **Causa:** El Firewall de Windows bloquea las conexiones entrantes al puerto 8080.
* **Solución:** Cuando Windows te muestre la alerta de seguridad al ejecutar `wp server`, marca la casilla de **Redes privadas** y haz clic en **Permitir acceso**.

### 3. Error de codificación en PowerShell
Si ves caracteres extraños en la consola de PowerShell al ejecutar comandos de WP-CLI:
```powershell
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
```
