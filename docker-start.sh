#!/bin/sh
# Render nos da el puerto en la variable PORT
sed -i "s/80/${PORT:-80}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

php artisan config:cache
php artisan migrate --force

# los seeders solo se corren cuando RUN_SEED=true (si no, se duplicarían en cada reinicio)
if [ "$RUN_SEED" = "true" ]; then
  php artisan db:seed --force
fi

apache2-foreground
