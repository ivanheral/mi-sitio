# 🛠️ skills.md — Catálogo de Habilidades y Recetas Técnicas

Este documento reúne las habilidades operativas, comandos de terminal, plantillas de código y procedimientos estándar para el desarrollo del blog de recetas de cocina en WordPress.

---

## ⚡ 1. Habilidades de Automatización con WP-CLI

### A. Creación de Contenido de Prueba para Recetas
```bash
# Crear una receta de prueba con meta campos y taxonomías
wp post create --post_type=receta \
  --post_title="Paella Valenciana Tradicional" \
  --post_status=publish \
  --post_content="Una receta clásica y auténtica con arroz, pollo, conejo y judías verdes." \
  --meta_input='{"_receta_tiempo_preparacion":"PT30M","_receta_tiempo_coccion":"PT45M","_receta_porciones":4,"_receta_dificultad":"Media"}'
```

### B. Gestión de Taxonomías de Cocina
```bash
# Añadir términos a la taxonomía 'tipo_cocina'
wp term create tipo_cocina "Mediterránea" --description="Cocina basada en ingredientes frescos y aceite de oliva"
wp term create tipo_cocina "Mexicana"
wp term create tipo_cocina "Asiática"

# Añadir términos a la taxonomía 'dieta'
wp term create dieta "Vegetariana"
wp term create dieta "Sin Gluten"
wp term create dieta "Keto"
```

### C. Mantenimiento y Backups Rápidos
```bash
# Exportar base de datos antes de una modificación crítica
wp db export ~/Escritorio/mi-sitio/backup_recetas_$(date +%Y%m%d).sql

# Regenerar miniaturas tras añadir un nuevo tamaño de imagen de receta
wp media regenerate --yes
```

---

## 🍳 2. Habilidad: Marcado Estructurado Schema.org (JSON-LD)

El marcado Schema es el corazón del SEO de un blog gastronómico. La siguiente plantilla PHP genera el bloque JSON-LD que Google requiere para mostrar estrellas, fotos y tiempos en los resultados de búsqueda:

```php
/**
 * Genera el JSON-LD para Google Rich Results (Recipe).
 */
function recetas_generar_schema_jsonld( $post_id ) {
    $meta = get_post_meta( $post_id );
    $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
    $author_name = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );

    $schema = [
        '@context'            => 'https://schema.org',
        '@type'               => 'Recipe',
        'name'                => get_the_title( $post_id ),
        'image'               => $image_url ? [ $image_url ] : [],
        'description'         => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
        'author'              => [
            '@type' => 'Person',
            'name'  => $author_name,
        ],
        'datePublished'       => get_the_date( 'Y-m-d', $post_id ),
        'prepTime'            => $meta['_receta_tiempo_preparacion'][0] ?? 'PT20M',
        'cookTime'            => $meta['_receta_tiempo_coccion'][0] ?? 'PT30M',
        'totalTime'           => 'PT50M',
        'recipeYield'         => ( $meta['_receta_porciones'][0] ?? '4' ) . ' porciones',
        'recipeCategory'      => wp_get_post_terms( $post_id, 'categoria_plato', ['fields' => 'names'] ),
        'recipeCuisine'       => wp_get_post_terms( $post_id, 'tipo_cocina', ['fields' => 'names'] ),
        'recipeIngredient'    => maybe_unserialize( $meta['_receta_ingredientes'][0] ?? [] ),
        'recipeInstructions'  => array_map( function( $paso ) {
            return [
                '@type' => 'HowToStep',
                'text'  => $paso,
            ];
        }, maybe_unserialize( $meta['_receta_pasos'][0] ?? [] ) ),
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', function() {
    if ( is_singular( 'receta' ) ) {
        recetas_generar_schema_jsonld( get_the_ID() );
    }
} );
```

---

## 📱 3. Habilidad: UX de Cocina Interactiva

### A. Modo Cocina (Wake Lock API)
Evita que la pantalla del teléfono o tablet del usuario se apague mientras tiene las manos con harina o masa:

```javascript
// Activar Wake Lock al marcar "Modo Cocina"
let wakeLock = null;

async function toggleModoCocina(activo) {
    if (activo && 'wakeLock' in navigator) {
        try {
            wakeLock = await navigator.wakeLock.request('screen');
            console.log('Pantalla bloqueada para permanecer activa.');
        } catch (err) {
            console.error('Error al solicitar Wake Lock:', err);
        }
    } else if (wakeLock) {
        await wakeLock.release();
        wakeLock = null;
    }
}
```

### B. Estilo de Impresión Limpio (`@media print`)
Para que el usuario pueda imprimir solo los ingredientes y pasos sin publicidad, cabeceras ni pie de página:

```css
@media print {
    /* Ocultar elementos no relevantes */
    header, nav, footer, .sidebar, .comments-area, .btn-modo-cocina, .ad-banner {
        display: none !important;
    }

    /* Formato legible en papel */
    body {
        font-family: serif;
        font-size: 12pt;
        color: #000;
        background: #fff;
    }

    .tarjeta-receta {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }

    /* Forzar que los pasos no se corten a mitad de página */
    .paso-receta {
        page-break-inside: avoid;
    }
}
```

---

## 🎨 4. Habilidad: Registro Modular de CPT y Taxonomías

Plantilla para registrar el tipo de post `receta` con soporte completo para Gutenberg (`show_in_rest => true`):

```php
function recetas_registrar_post_type() {
    $labels = [
        'name'               => __( 'Recetas', 'recetas-domain' ),
        'singular_name'      => __( 'Receta', 'recetas-domain' ),
        'add_new_item'       => __( 'Añadir Nueva Receta', 'recetas-domain' ),
        'edit_item'          => __( 'Editar Receta', 'recetas-domain' ),
        'all_items'          => __( 'Todas las Recetas', 'recetas-domain' ),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-food',
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ],
        'show_in_rest'       => true, // Permite el editor de bloques Gutenberg
        'rewrite'            => [ 'slug' => 'recetas' ],
    ];

    register_post_type( 'receta', $args );
}
add_action( 'init', 'recetas_registrar_post_type' );
```
