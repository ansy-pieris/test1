#!/usr/bin/env sh
set -e

echo "[startup] Beginning container initialization"

# Resolve runtime port (Railway injects PORT). Fallback 8080.
PORT_VALUE="${PORT:-8080}"

# --- Database env normalization -------------------------------------------------
# Railway exposes MySQL variables typically as: MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQL_URL
# We previously mapped with underscores; ensure Laravel vars exist now.
if [ -n "${MYSQLHOST}" ] && [ -z "${DB_HOST}" ]; then
  export DB_HOST="${MYSQLHOST}"
  export DB_PORT="${MYSQLPORT}"
  export DB_DATABASE="${MYSQLDATABASE}"
  export DB_USERNAME="${MYSQLUSER}"
  export DB_PASSWORD="${MYSQLPASSWORD}"
fi

# Optional: if MYSQL_URL present, export DB_URL so Laravel can parse it (Laravel 10+/12 supports this)
if [ -n "${MYSQL_URL}" ] && [ -z "${DB_URL}" ]; then
  export DB_URL="${MYSQL_URL}"
fi

echo "[startup] DB_HOST=${DB_HOST:-'(empty)'} DB_DATABASE=${DB_DATABASE:-'(empty)'}"

# --- Cache config/routes/views only once per container -------------------------
if [ ! -f storage/framework/cache/.optimized_done ]; then
  echo "[startup] Caching Laravel config/routes/views"
  php artisan config:clear || true
  php artisan cache:clear || true
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
  mkdir -p storage/framework/cache && touch storage/framework/cache/.optimized_done
fi

# --- Wait for MySQL (up to 60s) -------------------------------------------------
if [ -n "${DB_HOST}" ]; then
  echo "[startup] Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}"
  ATTEMPTS=0
  until php -r 'exit(@fsockopen(getenv("DB_HOST"), getenv("DB_PORT") ?: 3306) ? 0 : 1);'; do
    ATTEMPTS=$((ATTEMPTS+1))
    if [ $ATTEMPTS -ge 12 ]; then
      echo "[startup] MySQL still unreachable after ${ATTEMPTS} attempts – continuing without migrations"
      break
    fi
    echo "[startup] MySQL not ready (attempt ${ATTEMPTS}), retrying in 5s..."
    sleep 5
  done
fi

# --- Run migrations (retry lightly) --------------------------------------------
if [ -n "${DB_HOST}" ]; then
  for i in 1 2 3; do
    if php artisan migrate --force; then
      echo "[startup] Migrations complete"
      break
    else
      echo "[startup] Migration attempt ${i} failed; retrying in 4s";
      sleep 4;
    fi
  done
fi

# --- (Optional) Seed once if no products exist ---------------------------------
if php artisan tinker --execute='echo \App\\Models\\Product::count();' 2>/dev/null | grep -q '^0$'; then
  echo "[startup] Seeding database (products empty)"
  php artisan db:seed --force || echo "[startup] Seeding skipped (failure)"
fi

echo "[startup] Launching FrankenPHP on port ${PORT_VALUE}"
exec frankenphp php-server --listen :${PORT_VALUE} --root /app/public
