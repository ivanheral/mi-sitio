# WordPress project guidelines

This repository is a clean WordPress starter installation using the unmodified WordPress core and the official Twenty Twenty-Five block theme. Keep project-specific functionality out of core files.

## Engineering principles

- Prefer native WordPress APIs, Gutenberg blocks, and theme settings before adding plugins or custom code.
- Keep custom functionality in a dedicated plugin and visual changes in a child theme or a separate custom theme.
- Follow WordPress Coding Standards and support the WordPress/PHP versions declared by the project.
- Sanitize input, validate permissions and nonces for state-changing requests, and escape output at render time.
- Keep dependencies minimal. Do not activate or add plugins without a clear requirement.
- Preserve the behavior of the stock core and official theme; do not modify `wp-admin/`, `wp-includes/`, or bundled themes.

## Workflow

1. Review `tasks.md` and the relevant project files before making changes.
2. Make focused changes and update directly related documentation.
3. Validate PHP with `php -l`, JSON with a JSON parser, and run the narrowest applicable tests.
4. Verify the WordPress site and WP-CLI commands when the local database is available.
5. Keep credentials, database exports, uploads, and local configuration out of version control.
