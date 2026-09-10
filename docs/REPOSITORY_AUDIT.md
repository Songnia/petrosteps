# Repository Audit Report

## Project

Petrosteps is a role-based oilfield lifecycle training simulator. Participants progress through licensing, survey, exploration, appraisal, development, production, secondary recovery, and abandonment while the product calculates operational and financial indicators.

## Repository

<https://github.com/Songnia/petrosteps>

Main branch: `main`

## Production URL

Not verified. A historical configuration referenced `zy-portal.com/petronew`, but the host did not resolve during the audit. No current deployment manifest or stable production URL exists in the repository.

## Stack Detected

- Frontend: server-rendered HTML, custom MD3 CSS, Bootstrap, jQuery, amCharts.
- Backend: procedural/object-oriented PHP 8 with flat page controllers and AJAX endpoints.
- Database: MySQL accessed through a shared PDO subclass.
- Authentication: username/password, PHP sessions, database-backed active-session check, four roles.
- Storage: MySQL and PHP session storage; no active object-storage integration detected.
- Payments: none detected.
- External APIs: none detected; public font and CSS CDNs are used.
- PDF: legacy embedded mPDF distribution.
- Build/package manager: none.
- CI/CD: PHP syntax workflow added during normalization.
- Automated tests: none detected.

## Architecture

The codebase is a PHP monolith. Requests are handled by root-level PHP pages; `includes/db.class.php` provides data access; project state is persisted in `tbl_project` and yearly snapshots in `tbl_project_step`. Shared admin and participant shells live under `includes/`. See [ARCHITECTURE.md](ARCHITECTURE.md).

## Security Findings

### Critical

No active secret, private key, production database dump, or `.env` file was found in the final tracked tree.

### High

- Passwords are stored and verified with MD5 rather than an adaptive password hash.
- Several search/list queries interpolate request-controlled values, creating SQL-injection risk if upstream cleaning or sort values are bypassed.
- Internal Office specifications were present in the first public commit. They are removed from the current tree but remain downloadable from Git history until an approved history rewrite.

### Medium

- Administrative write actions do not consistently use CSRF tokens.
- Object-level authorization is not centralized or comprehensively tested.
- The embedded mPDF version is legacy and has known PHP compatibility debt.
- Secure cookies and HTTP security headers depend on hosting configuration.

### Low

- The repository has no automated test suite or dependency lockfile.
- Some older pages still load third-party assets over HTTP or mix frontend library generations.

## Sensitive Files Found

- Local legacy database-class copies containing historical credentials.
- Full SQL exports containing users and project data.
- Internal DOC, DOCX, and PPTX product specifications.

Secret values are intentionally not reproduced in this report.

## Sensitive Files Removed

Legacy database configurations and full database exports were excluded before the first public commit. Internal Office documents are removed from the current Git tree. Their local copies remain ignored for maintainer use.

## .gitignore Changes

The ignore policy now covers environment files, credentials, keys, logs, uploads, database dumps, local storage, editor files, OS metadata, Office temporary files, legacy configurations, internal specifications, diagnostics, repair scripts, stale assets, and unused mPDF examples.

## Environment Changes

Application URL, encryption key, and database connection values use process environment variables. `.env.example` contains placeholders only. This application does not include an automatic dotenv loader.

## Git History Status

A complete pre-cleanup backup exists at `/tmp/petrosteps-before-normalization.bundle`. After explicit maintainer approval, `main` was rebuilt from the sanitized tree so the internal Office documents are no longer part of the public branch history.

## README Changes

A product-specific README now documents the problem, workflows, role model, architecture, stack, features, engineering challenges, screenshot, local setup, testing, deployment, security, status, and author.

## GitHub Description

Proposed description:

> Interactive oilfield lifecycle training platform connecting technical decisions, production, costs, revenue and cash flow.

## GitHub Topics

Proposed topics: `php`, `mysql`, `oil-and-gas`, `simulation`, `training-platform`, `data-visualization`, `product-engineering`.

GitHub About settings were not changed because the available GitHub CLI authentication is invalid.

## Files Removed

- Internal product specifications under `database/oilstepsdata/`.
- Unused mPDF examples and sample media.
- One-off debug and repair scripts.
- Obsolete formula notes, backup navigation files, and unreferenced chart/image artifacts.

## Files Added

- `README.md` and `CONTRIBUTING.md`.
- Architecture, deployment, security, and repository audit documentation.
- An anonymized MySQL schema and reference-data export.
- A GitHub Actions PHP syntax workflow.

## Files Modified

- `.gitignore` was expanded for the detected PHP/MySQL stack.
- Runtime database and application secrets had already been moved to environment variables before this audit.

## Tests

- All application PHP files passed `php -l` under PHP 8.3.6.
- No unit, integration, browser, or security regression suite exists.

## Build

No build step exists. The application is served directly by PHP.

## Deployment Verification

- Local `landing.php`: HTTP 200.
- Local `login.php`: HTTP 200.
- Local unauthenticated `index.php`: HTTP 302 to `landing.php`.
- The anonymized schema and reference data imported successfully into a temporary MySQL database.
- Authenticated role workflows were not browser-tested during this repository audit.
- Public production could not be reached or identified.

## Remaining Risks

- Replace MD5 authentication before using the application for sensitive accounts.
- Parameterize remaining dynamic SQL and allowlist all sort expressions.
- Add CSRF protection and centralized authorization checks.
- Upgrade mPDF through Composer.
- Add automated calculation, authorization, and end-to-end workflow tests.
- Validate production hosting, backups, TLS, headers, and rollback behavior.

## Manual Actions Required

1. Rotate historical database credentials as a precaution.
2. Reauthenticate GitHub CLI and set the proposed description and topics.
3. Confirm a stable production URL before adding a repository website link.
4. Confirm project ownership and choose an explicit open-source or proprietary license.

## Final Repository Readiness

| Area | Score |
| --- | ---: |
| Security | 6/10 |
| Documentation | 9/10 |
| Repository Hygiene | 8/10 |
| Reproducibility | 7/10 |
| Recruiter Readiness | 9/10 |
| Production Safety | 5/10 |

**OVERALL REPOSITORY READINESS: 73/100**

Status: **READY FOR PUBLIC REVIEW**. Authentication hardening and the documented production-safety work remain important before handling sensitive accounts.
