# WordPress starter site

This repository contains a standard WordPress installation with the official Twenty Twenty-Five block theme. The WordPress core and bundled themes are kept unmodified; no custom theme or active plugin is required.

## Guides

| Platform | Setup guide |
|---|---|
| Linux (CachyOS/Arch, Ubuntu/Debian, Fedora) | [Linux guide](docs/README_LINUX.md) |
| macOS | [macOS guide](docs/README_MACOS.md) |
| Windows | [Windows guide](docs/README_WINDOWS.md) |
| Cross-platform setup, WP-CLI, themes, staging, and deployment | [Multiplatform guide](GUIA_MULTIPLATAFORMA.md) |

## Local development

Start MariaDB/MySQL, then from this project directory run:

```bash
wp core is-installed
wp server --port=8080
```

Open [http://localhost:8080](http://localhost:8080) or [the WordPress dashboard](http://localhost:8080/wp-admin). Stop the development server with `Ctrl+C`.

The local server is for development only; use a WordPress-capable host, HTTPS, and a separate production database for a public website.

## Current starter configuration

- Active theme: `twentytwentyfive` (official, unmodified block theme)
- Plugins: none active by default
- Content: demo post and page removed; the standard privacy-policy draft is preserved
- Languages and platform-specific setup: see the guides above

## WP-CLI

```bash
wp theme list
wp theme activate twentytwentyfive
wp post create --post_type=page --post_title="About" --post_status=draft
wp db export backup.sql
```

See [skills.md](skills.md) for common WP-CLI operations and [tasks.md](tasks.md) for optional starter improvements.

## Security

Do not commit `wp-config.php`, passwords, database dumps, or private uploads. Replace all local development credentials before deploying the site.
