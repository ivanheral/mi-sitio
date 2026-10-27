# WordPress and WP-CLI reference

Run WP-CLI commands from the directory containing `wp-config.php`. Keep a database backup before migrations or bulk updates.

## Site health and themes

```bash
wp core is-installed
wp core version
wp theme list
wp theme list --status=active
wp theme activate twentytwentyfive
```

Install a theme from the official directory by its slug:

```bash
wp theme install twentytwentyfour --activate
```

## Draft and publish content

Create content as a draft, review it in the WordPress editor, then publish it from the dashboard or with WP-CLI:

```bash
wp post create --post_type=page --post_title="About" --post_status=draft
wp post list --post_status=draft --fields=ID,post_type,post_title
wp post update ID --post_status=publish
```

Replace `ID` with the post ID returned by the list command.

## Database backup and migration

```bash
wp db export backup.sql
wp db check
```

After copying the files and importing the database into a separate destination installation, replace the source URL with the destination URL:

```bash
wp search-replace 'http://localhost:8080' 'https://example.com' --skip-columns=guid
wp option update home 'https://example.com'
wp option update siteurl 'https://example.com'
```

Review the destination before directing visitors to it. Do not expose `wp server` as a production web server.

## Debugging

For local development, enable `WP_DEBUG` and `WP_DEBUG_LOG` in `wp-config.php` while keeping `WP_DEBUG_DISPLAY` disabled. Do not enable public error display on a live site.
