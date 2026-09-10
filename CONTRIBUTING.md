# Contributing

## Workflow

1. Create a focused branch from `main`.
2. Keep credentials and production data outside Git.
3. Make small changes that preserve the existing PHP architecture.
4. Run PHP syntax validation before opening a pull request.
5. Document database changes as an additive SQL migration.

## Validation

```bash
find . -path './mpdf' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

For simulation changes, manually validate the affected lifecycle step, project timeline, KPI values, graph, and report.

## Commit Style

Use descriptive commits such as:

```text
fix: prevent duplicate exploration spending
feat: add configurable production opex
docs: document deployment requirements
security: move database credentials to environment variables
```
