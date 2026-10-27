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

# Crear la base de datos y usuario para WordPress.
# Reemplaza CAMBIA_ESTA_CLAVE por una contraseña única antes de ejecutar.
sudo mariadb -e "CREATE DATABASE misitio_db; CREATE USER 'wp_user'@'localhost' IDENTIFIED BY 'CAMBIA_ESTA_CLAVE'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'wp_user'@'localhost';"
```

#### Paso 4: Descargar y arrancar WordPress
```bash
mkdir -p ~/Escritorio/mi-sitio && cd ~/Escritorio/mi-sitio
wp core download
wp config create --dbname=misitio_db --dbuser=wp_user --dbpass='CAMBIA_ESTA_CLAVE'
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

# Crear base de datos y usuario (cambia el marcador por una clave única)
mariadb -u root -e "CREATE DATABASE misitio_db; CREATE USER 'wp_user'@'localhost' IDENTIFIED BY 'CAMBIA_ESTA_CLAVE'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'wp_user'@'localhost';"
```

#### Paso 4: Descargar y arrancar WordPress
```bash
mkdir -p ~/Desktop/mi-sitio && cd ~/Desktop/mi-sitio
wp core download
wp config create --dbname=misitio_db --dbuser=wp_user --dbpass='CAMBIA_ESTA_CLAVE'
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
   sudo mariadb -e "CREATE DATABASE misitio_db; CREATE USER 'wp_user'@'localhost' IDENTIFIED BY 'CAMBIA_ESTA_CLAVE'; GRANT ALL PRIVILEGES ON misitio_db.* TO 'wp_user'@'localhost';"
   
   # Crear sitio y correr servidor
   mkdir ~/mi-sitio && cd ~/mi-sitio
   wp core download
   wp config create --dbname=misitio_db --dbuser=wp_user --dbpass='CAMBIA_ESTA_CLAVE'
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

## 🎨 Cambiar el tema con WP-CLI

Ejecuta estos comandos desde la carpeta que contiene `wp-config.php`, con MariaDB/MySQL activo. Antes de cambiar el tema, crea una copia de seguridad y comprueba qué temas están instalados:

```bash
wp db export backup-antes-del-tema.sql
wp theme list
```

### Dónde encontrar temas instalables

El catálogo oficial y gratuito de temas está en [wordpress.org/themes](https://wordpress.org/themes/). Puedes filtrar por funciones, diseño y tipo de sitio, abrir la ficha de cada tema para revisar capturas, descripción y requisitos, y probar una vista previa antes de decidir.

También puedes explorar e instalar temas desde el escritorio de WordPress: **Apariencia → Temas → Añadir un tema nuevo**. Busca por nombre o filtra el catálogo; selecciona **Vista previa** para probarlo y **Instalar** cuando quieras añadirlo. Instalarlo no lo activa automáticamente: después elige **Activar**.

Existen temas comerciales en sitios de sus propios desarrolladores y mercados de terceros. Antes de instalar uno, verifica que provenga de una fuente de confianza, que sea compatible con tu versión de WordPress y que tenga actualizaciones recientes y soporte. Evita paquetes de temas obtenidos de sitios no oficiales.

### Instalar y activar un tema

Desde WP-CLI puedes instalar un tema del catálogo oficial indicando su *slug* (el identificador que aparece en la ficha o en la URL del catálogo):

```bash
wp theme install twentytwentyfour
wp theme activate twentytwentyfour
```

O instalarlo y activarlo en un solo comando:

```bash
wp theme install twentytwentyfour --activate
```

Para un tema comercial o propio que tengas como archivo ZIP, cárgalo desde **Apariencia → Temas → Añadir un tema nuevo → Subir tema**, o usa WP-CLI con la ruta al ZIP:

```bash
wp theme install /ruta/al/tema.zip --activate
```

No descomprimas el archivo dentro de la carpeta de temas si el ZIP contiene documentación u otros archivos; usa la opción de instalación para que WordPress procese el paquete.

Para activar uno de los temas que ya incluye esta instalación:

```bash
wp theme activate twentytwentyfive
```

El tema cambia la presentación, no elimina las entradas, páginas ni medios de la biblioteca. Sin embargo, menús, widgets y plantillas personalizadas pueden variar entre temas; revisa la portada y las páginas después del cambio. Para ver cuál está activo:

```bash
wp theme list --status=active
```

## 📝 Crear y publicar contenido con WP-CLI

Puedes crear primero un borrador y revisar el contenido antes de hacerlo público:

```bash
# Crear una página como borrador
wp post create \
  --post_type=page \
  --post_title="Acerca de este sitio" \
  --post_status=draft

# Usa el ID que devolvió el comando anterior para revisar y publicar
wp post get ID --fields=ID,post_title,post_status
wp post update ID --post_status=publish
```

Reemplaza `ID` por el número que devolvió `wp post create`, por ejemplo `42`.

Para publicar una entrada directamente, cambia `--post_type=page` por `--post_type=post` y `--post_status=draft` por `--post_status=publish`. Comprueba el contenido público en el sitio después de publicar:

```bash
wp post list --post_status=publish --fields=ID,post_type,post_title,url
```

Publicar una entrada o página la hace visible según los ajustes de privacidad y visibilidad del sitio. **No publica por sí solo el sitio en Internet.**

## 🧪 Publicar una copia en una URL de pruebas

Para compartir una versión de prueba, crea un **staging** (una copia independiente del sitio) en un alojamiento compatible con WordPress. Muchos alojamientos ofrecen una función de staging con una URL temporal, o puedes crear un subdominio como `pruebas.midominio.com`. Si aún no tienes dominio, algunos alojamientos proporcionan una URL temporal. La URL y los pasos para crearla dependen del proveedor; busca en su panel opciones como **Staging**, **Clonar sitio** o **URL temporal**.

No uses `wp server --port=8080` como sitio público: es un servidor de desarrollo local. Tampoco abras directamente a Internet tu equipo de desarrollo para compartir una prueba. La opción recomendada es el staging del alojamiento, con su propio directorio y una base de datos separada.

### Opciones gratuitas

| Servicio | Para qué sirve | Límites importantes |
|---|---|---|
| [InfinityFree](https://www.infinityfree.com/) | Alojamiento gratuito con PHP y MySQL, subdominio incluido y opción de instalar WordPress. Es la opción a considerar si quieres probar una instalación WordPress autohospedada como la de este repositorio. | Los recursos y el soporte de un plan gratuito son limitados; consulta sus condiciones vigentes y conserva copias propias. No lo elegiría para un sitio crítico o con tráfico importante sin comprobar antes su rendimiento y políticas. |
| [WordPress.com](https://wordpress.com/free/) | Crear rápidamente un sitio con dirección gratuita bajo `wordpress.com`, sin configurar PHP, base de datos ni servidor. | En el plan gratuito se muestran anuncios. No permite instalar plugins ni subir temas de terceros, así que no es el lugar adecuado para ejecutar íntegramente un proyecto autohospedado con personalizaciones propias. |
| [TasteWP](https://tastewp.com/) | Crear un WordPress temporal para probar un tema o plugin y compartir una demostración. | Es un entorno de prueba, no un alojamiento para mantener publicado el sitio de forma permanente. |

**Recomendación para este proyecto:** si quieres probar esta instalación y conservar la posibilidad de usar temas o plugins propios, empieza por un hosting PHP/MySQL como InfinityFree y crea allí una instalación independiente. Si solo quieres enseñar una maqueta rápidamente, WordPress.com es más sencillo; para una prueba desechable de un tema, usa TasteWP. Las condiciones de los planes gratuitos pueden cambiar, así que confírmalas con el proveedor antes de migrar.

### Pasos generales

1. Crea el sitio de pruebas desde el panel del alojamiento y configura HTTPS. Protege el acceso con contraseña o con controles de acceso del proveedor si solo debe verlo tu equipo.
2. Haz un respaldo de la base de datos y de los archivos del sitio. En el sitio local, desde el directorio de WordPress:

   ```bash
   wp db export backup-staging.sql
   ```

3. Copia al staging los archivos de WordPress necesarios, incluido `wp-content/uploads`, junto con el tema y los plugins. Importa el respaldo en la base de datos **vacía y exclusiva del staging** desde el panel del alojamiento o con WP-CLI:

   ```bash
   wp db import backup-staging.sql
   ```

   Configura el `wp-config.php` del staging con las credenciales de esa base de datos, no con las del entorno local ni con las de producción.

4. Desde el directorio de WordPress del staging, reemplaza la URL local por la URL de prueba. Sustituye ambas direcciones por las reales de tu instalación:

   ```bash
   wp search-replace 'http://localhost:8080' 'https://pruebas.midominio.com' --skip-columns=guid
   wp option update home 'https://pruebas.midominio.com'
   wp option update siteurl 'https://pruebas.midominio.com'
   wp core is-installed
   ```

   `wp search-replace` actualiza también datos serializados de WordPress. No hagas un reemplazo de URL en producción sin un respaldo previo.

5. Impide que los buscadores indexen la copia y comprueba su configuración de privacidad en **Ajustes → Lectura**:

   ```bash
   wp option update blog_public 0
   ```

   Este ajuste es una solicitud a los buscadores, **no** protege el sitio ni garantiza que no se indexe. Para una prueba privada, añade también contraseña o restricción de acceso desde el alojamiento.

6. Visita la URL de pruebas y revisa inicio de sesión, páginas, imágenes, enlaces, formularios y vista móvil. Comprueba especialmente que correos, pagos, webhooks y otras integraciones no afecten a usuarios reales. No copies credenciales de producción si el entorno de pruebas no las necesita.

Mantén la base de datos y los archivos del staging separados de producción. Al terminar la prueba, elimina el staging desde el panel del alojamiento o actualízalo siguiendo el flujo de trabajo de ese proveedor.

## 🌍 Preparar un sitio para producción

Para que el sitio sea accesible públicamente necesitas un alojamiento con PHP y MariaDB/MySQL, un dominio configurado y HTTPS. `wp server` es un servidor temporal de desarrollo: no lo expongas como servidor de producción.

Antes de migrar, realiza una copia de la base de datos y conserva también los archivos del sitio, especialmente `wp-content/uploads`, los temas y los plugins:

```bash
wp db export backup-antes-de-migrar.sql
```

En el servidor de destino, instala o copia los archivos, importa la base de datos y configura `wp-config.php` con las credenciales de producción. Desde la carpeta de WordPress en el servidor, actualiza las URL usando WP-CLI, que gestiona correctamente los datos serializados:

```bash
wp search-replace 'http://localhost:8080' 'https://midominio.com' --skip-columns=guid
wp option update home 'https://midominio.com'
wp option update siteurl 'https://midominio.com'
wp core is-installed
```

Sustituye `https://midominio.com` por el dominio real. Verifica que DNS y HTTPS estén configurados, inicia sesión en `/wp-admin` y revisa páginas, imágenes, enlaces permanentes y formularios. Guarda una copia de seguridad antes de reemplazar URL o importar una base de datos.

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

# 4. Ver temas y activar uno ya instalado
wp theme list
wp theme activate twentytwentyfive

# 5. Exportar copia de seguridad de la base de datos
wp db export backup.sql

# 6. Importar base de datos
wp db import backup.sql

# 7. Cambiar URLs con seguridad (por ejemplo, al migrar a producción)
wp search-replace 'http://localhost:8080' 'https://midominio.com' --skip-columns=guid
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
