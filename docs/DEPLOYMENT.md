# Deployment

## Runtime

Petrosteps requires PHP with PDO MySQL, MySQL, and a writable PHP session directory. Apache with mod_php or Nginx/Apache with PHP-FPM can host the application.

## Configuration

Set these values through the hosting platform or web-server environment:

```env
APP_URL=https://example.com
APP_ENCRYPT_KEY=replace-with-a-long-random-secret
DB_HOST=database-host
DB_DATABASE=database-name
DB_USERNAME=database-user
DB_PASSWORD=database-password
```

Never deploy `.env` through Git. The code currently provides development defaults only when running locally.

## Database

For a new environment:

```bash
mysql -u USER -p DATABASE < database/schema.sql
mysql -u USER -p DATABASE < database/reference-data.sql
```

The reference-data file contains no users, sessions, or participant projects. Create environment-specific administrators separately.

## Web Server

- Point the document root at the repository root because existing routes are flat PHP files.
- Disable directory listing.
- Deny HTTP access to `.env`, `.git`, `database/`, `docs/`, and `storage/`.
- Redirect HTTP to HTTPS and set secure session-cookie flags at the PHP/web-server level.
- Keep database and application backups outside the public document root.

## Release Check

```bash
find . -path './mpdf' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

Then verify login, each role dashboard, one complete project lifecycle, graph continuity, report rendering, and responsive navigation.

## Production Status

No CI/CD configuration or stable production URL was present in the audited source. Production deployment and rollback procedures therefore require validation with the hosting owner before automation.
