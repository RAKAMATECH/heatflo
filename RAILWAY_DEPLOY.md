# Railway Deployment

Deploy this Laravel API as a Docker service from the `heatflo-api` directory.

## Required Railway variables

```env
APP_NAME=Heatflo
APP_ENV=production
APP_KEY=base64:TCIHHglE+VUJek5vlEJCY/bIYSADxm7EXlp7dSCFEJs=
APP_DEBUG=false
APP_URL=https://YOUR-RAILWAY-DOMAIN.up.railway.app
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=YOUR_DB_HOST
DB_PORT=3306
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD
CACHE_DRIVER=file
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
FRONTEND_URL=https://heatflo.co.zw
SANCTUM_STATEFUL_DOMAINS=heatflo.co.zw,www.heatflo.co.zw
LEAD_NOTIFY_TO=info@heatflo.co.zw
DEMO_CATALOG_SEED=false
RUN_MIGRATIONS=true
RUN_SEEDERS=true
```

Set `RUN_MIGRATIONS=false` and `RUN_SEEDERS=false` after the first successful deploy if you do not want migrations/seeders to run on every restart.

## Deploy steps

1. Push this repository to GitHub.
2. In Railway, create a new project from the GitHub repository.
3. Set the service root directory to `heatflo-headless-frontend/heatflo-api` if Railway asks for a root path.
4. Add a MySQL database service or connect an external MySQL database.
5. Add the variables above using the real Railway app URL and database values.
6. Deploy.

## Test after deploy

Open:

```text
https://YOUR-RAILWAY-DOMAIN.up.railway.app/api/catalog/products
```

After it returns JSON, rebuild the static frontend with:

```env
NEXT_PUBLIC_LARAVEL_API_BASE_URL=https://YOUR-RAILWAY-DOMAIN.up.railway.app
LARAVEL_API_BASE_URL=https://YOUR-RAILWAY-DOMAIN.up.railway.app
NEXT_PUBLIC_SITE_URL=https://heatflo.co.zw
```
