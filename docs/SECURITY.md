# Security

## Reporting a Vulnerability

Do not open a public issue containing credentials, personal data, or exploit details. Contact the repository owner privately through the profile at <https://github.com/Songnia>.

## Repository Controls

- Runtime secrets are read from environment variables.
- `.env` files, private keys, database dumps, logs, uploads, and legacy credential-bearing configuration copies are ignored.
- Public database fixtures contain reference data only, with no users, sessions, emails, or projects.
- A pre-normalization Git bundle is stored outside the repository for maintainer recovery.

## Known Security Debt

The following issues were found during the repository audit and must be addressed before treating the application as hardened production software:

1. Passwords use legacy MD5 hashing. Migrate accounts to `password_hash()` and verify them with `password_verify()`.
2. Some list/search methods interpolate input into SQL. Replace them with bound parameters and allowlists for sort columns and directions.
3. Administrative POST actions do not consistently enforce CSRF tokens.
4. Authorization relies mainly on page-level session checks; object-level ownership checks need systematic coverage.
5. The embedded mPDF release is legacy and should be replaced with a maintained Composer dependency.
6. Security headers and secure cookie attributes depend on server configuration and are not centrally enforced by the application.
7. Automated security and integration tests are not yet available.

## Secret Rotation

Legacy database credentials were found in local historical configuration copies. They were excluded before the first public commit and were not found in tracked text history. Even so, any credential that has been shared outside its intended environment should be rotated.

## Supported Version

The `main` branch represents active development. No long-term support policy has been established.
