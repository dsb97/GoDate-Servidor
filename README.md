<p align="center"><img src="https://raw.githubusercontent.com/dsb97/GoDate-Cliente/main/src/assets/images/LogoIzq.png" width="400"></p>

## GoDate

Pasos para desplegar la aplicación en local:

- Crear una base de datos llamada GoDate.
- Crear un fichero de nombre .env.example en el directorio raíz del proyecto con el siguiente contenido:
```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

```
- Ejecutar los siguientes comandos en el directorio raíz del proyecto:

```
cp .env.example .env

php artisan key:generate

composer update
```
- El script de creación de la BBDD está en la entrega del Desafío 2
- En phpmyadmin, ejecutar la siguiente sentencia (para que se muestren los usuarios, puesto que con los seeders se generan edades de preferencia con un intervalo muy pequeño y no se muestran los usuarios con los que se tiene una afinidad):

```
update preferencias_usuarios
set intensidad = 0
where id_preferencia = 7;

update preferencias_usuarios
set intensidad = 100
where id_preferencia = 8
```

