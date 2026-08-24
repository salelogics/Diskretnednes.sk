# Deployment Notes

## Storage directory

This project follows the **standard Laravel storage structure**. The
`storage/` directory and its subdirectories are committed to Git with
per-directory `.gitignore` files (the Laravel default), so a fresh
`git clone` produces a working tree with no manual fixes:

```
storage/
├── app/
│   ├── .gitignore
│   └── public/
│       └── .gitignore
├── framework/
│   ├── .gitignore
│   ├── cache/
│   │   ├── .gitignore
│   │   └── data/
│   │       └── .gitignore
│   ├── sessions/
│   │   └── .gitignore
│   ├── testing/
│   │   └── .gitignore
│   └── views/
│       └── .gitignore
└── logs/
    └── .gitignore
```

Each `.gitignore` ignores runtime files (`*`) while keeping the directory
itself tracked (`!.gitignore`). Nothing that Laravel writes at runtime
(logs, cached views, compiled config, sessions, uploaded files) is ever
committed.

### `public/storage` symlink

`php artisan storage:link` creates `public/storage -> storage/app/public`.
This is already handled by `deploy.sh` and `public/storage` is git-ignored.
The `storage/app/public` directory is committed (empty, via its
`.gitignore`) so `storage:link` always has a valid target.

## Production: shared storage symlink (IMPORTANT — do NOT commit it)

The repository previously committed a `storage` **symlink**:

```
storage -> ../shared/storage
```

This has been **removed** from Git. A committed symlink breaks every fresh
checkout and any deployment performed outside the production release
system (the symlink target does not exist in a plain clone).

If the production environment uses a zero-downtime / shared-storage layout
(release directories with a persistent `shared/storage`), the symlink must
be **created during deployment**, per release — it must **not** live in
Git. For example, as part of the release step (outside this repo's
`deploy.sh`, which is left unchanged):

```bash
# Run by the release system, after checking out a new release:
rm -rf "$RELEASE_PATH/storage"
ln -s "$SHARED_PATH/storage" "$RELEASE_PATH/storage"
```

The shared `storage` directory must itself contain the standard Laravel
subdirectories (`app/public`, `framework/{cache/data,sessions,testing,views}`,
`logs`) with the correct permissions.

`deploy.sh` is intentionally left untouched; it continues to run
`php artisan storage:link`, set permissions on `storage/*`, and cache
config/routes/views as before.
