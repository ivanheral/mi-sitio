# 🌐 WordPress Local en CachyOS (Puerto 8080)

Tu sitio WordPress local ya está configurado y funcionando con **MariaDB**, **PHP** y **WP-CLI**.

---

## 📌 Datos de Acceso y Estado del Entorno

| Parámetro | Valor / Enlace |
| :--- | :--- |
| **URL del sitio:** | [http://localhost:8080](http://localhost:8080) |
| **Panel de administración:** | [http://localhost:8080/wp-admin](http://localhost:8080/wp-admin) |
| **Ruta del proyecto:** | `~/Escritorio/mi-sitio` |
| **Base de datos:** | MariaDB (`misitio_db`) |
| **Servidor web:** | PHP Built-in Server (`wp server`) |
| **PHP Extensions activadas:** | `mysqli`, `pdo_mysql`, `gd` (`memory_limit = 512M`) |

---

## 🚀 Cómo Arrancar y Detener el Sitio

### 1. Arrancar el servidor
Siempre que quieras trabajar en la web, abre una terminal y ejecuta:

```bash
cd ~/Escritorio/mi-sitio
wp server --port=8080
```
*(Mantén esa terminal abierta mientras trabajes en la web).*

### 2. Detener el servidor
* En la terminal donde está corriendo, presiona **`Ctrl + C`**.

### 3. Servicio MariaDB (Base de datos)
MariaDB corre en segundo plano en tu sistema:
* **Ver estado:** `systemctl status mariadb`
* **Iniciar si está apagado:** `sudo systemctl start mariadb`
* **Iniciar automáticamente con el PC:** `sudo systemctl enable mariadb`

---

## 🌍 Idioma Español

Para configurar WordPress en español, ejecuta dentro de la carpeta:

```bash
cd ~/Escritorio/mi-sitio
wp language core install es_ES --activate
```

---

## 🛠️ Comandos Útiles de WP-CLI (Día a Día)

`wp-cli` te permite gestionar todo WordPress desde la consola sin necesidad de usar el panel web:

### 🧩 Gestión de Plugins
```bash
# Ver plugins instalados y su estado
wp plugin list

# Buscar un plugin en el repositorio oficial
wp plugin search woocommerce

# Instalar y activar un plugin (ejemplo: WooCommerce o Elementor)
wp plugin install woocommerce --activate
wp plugin install elementor --activate

# Desactivar o eliminar un plugin
wp plugin deactivate elementor
wp plugin delete elementor
```

### 🎨 Gestión de Temas
```bash
# Ver temas instalados
wp theme list

# Instalar y activar un tema (ejemplo: Astra)
wp theme install astra --activate
```

### 👥 Gestión de Usuarios
```bash
# Ver lista de usuarios registrados
wp user list

# Crear un nuevo administrador
wp user create dev admin@misitio.local --role=administrator --user_pass="tuPassword123"

# Resetear la contraseña de un usuario
wp user update admin --user_pass="nuevaPassword"
```

### 💾 Base de Datos y Backups
```bash
# Exportar copia de seguridad completa de la base de datos
wp db export copia_seguridad.sql

# Restaurar copia de seguridad
wp db import copia_seguridad.sql

# Abrir consola interactiva MySQL de la base de datos
wp db cli

# Optimizar y reparar tablas
wp db optimize
```

### 🔄 Buscar y Reemplazar URLs (Migraciones)
Si en el futuro cambias de dominio o puerto:
```bash
wp search-replace 'http://localhost:8080' 'https://miweb.com'
```

---

## 📂 Estructura de Carpetas

```text
~/Escritorio/mi-sitio/
├── wp-config.php           # Configuración de base de datos y claves de seguridad
├── wp-content/
│   ├── plugins/            # Tus plugins instalados
│   ├── themes/             # Tus plantillas / temas
│   └── uploads/            # Archivos multimedia (imágenes, PDFs) subidos
├── wp-admin/               # Archivos del núcleo de administración
└── wp-includes/            # Librerías internas del núcleo
```

---

## 💡 Consejo Pro
Si en algún momento prefieres una interfaz gráfica completa con soporte para certificados SSL locales (HTTPS), túneles para compartir con clientes (*Live Links*) o interceptor de correos (*Mailpit*), también tienes disponible **LocalWP** en tu sistema ejecutando `localwp &`.
