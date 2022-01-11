
# API HEART ONLINE

The Heart Online APIs are HTTP-based RESTful APIs that use OAuth 2.0 for authorization. API request and response bodies are formatted in JSON.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

The Laravel framework has a few system requirements. it's highly recommended that you use Docker as your local development environment.

The next link help you to configure a docker environment with php + nginx + mysql.

* https://gitlab.com/alejandro.cepeda/docker-php-fpm-mysql

However, if you are not using Docker, you will need to make sure your server meets the following requirements:
```
* PHP >= 7.2.5
* BCMath PHP Extension
* Ctype PHP Extension
* Fileinfo PHP extension
* JSON PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PDO PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Composer
* Git
```

### Installing

Log into GitLab Server

```
git clone https://gitlab.com/alejandro.cepeda/heart-online-api.git
cd heart-online-api
```

Laravel utilizes Composer to manage its dependencies. So, before using Laravel, make sure you have Composer installed on your machine.

Command in your terminal:
```
composer install
```
Copy .env.example file to .env on root folder.
```
cp .env.example .env (ubuntu)
```

Open your .env file and change:
```
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Run follow commands

```
php artisan key:generate
php artisan migrate 
php artisan db:seed
php artisan passport:install
```

## Authors

* **Alejandro Cepeda** - *alejandro.cepeda@tars.dev*
* **Cristian Narvaez** - *cristian.narvaez@tars.dev*

**##INSTALACION CON DOCKER-COMPOSE##**
heart-back en un nuevo equipo

Para montar el proyecto y poder trabajar se deben seguir los siguientes pasos:
1.Crear una carpeta heart con el siguiente contenido:
	/heart
	|---/app                     ---el proyecto contenido
	    	|---ARVHIVOS PROYECTO---***
		|---.env	
        |---/nginx                   ---configuración nginxi
		|---cgi.conf      
		|---default.conf  
		|---nginx.conf 
	|---/php-fpm                 ---configuración php
		|---custom.php.ini   
		|---Dockerfile       
		|---entrypoint.sh    
		|---scheduler        
		|---websockets.conf 
	|---/mysql                   ---configuración mysql
        |---/docker-compose.yml      ---conf contenedores
        |---/oauth-public.key       ---archivo necesario
        |---/oauth-private.key       ---archivo necesario
        |---/respaldo20210901.sql    ---bk base de datos
	|---/.env                    ---variables de entorno
2.Levantar los contenedores con el siguiente comando, ejecutado desde heart/:
	***PARA ESTE PASO ES NECESARIO TENER DOCKER DOCKER-COMPOSE***
	docker-compose up -d         ---contenedores en segundo plano
	**PUERTOS 80,9001,33060 HABILITADOS**  --- cat docker-compose.yml para validar los puertos
	Si no se montaron correctamente, ejecutar:
	    docker-compose down
	y volver a ejecutar: 
        docker-compose up -d 
	**ejecutar docker ps para validar que los contenedores se esten ejecutando**
3.Crear base de datos en contenedor mysql:
	- Movemos el script sql al contenedor
                docker cp respaldo20210901.sql laravel-mysql:/respaldo20210901.sql
	- Entramos al contenedor mysql, ejecutamos:
		docker exec -ti laravel-mysql bash
	- Entramos a mysql y creamos la base de datos:
		mysql -uroot -proot
		create database heartonline;
		exit
	- Restaurar base de datos:
		mysqldump -uroot heartonline < respaldo20210901.sql
4.Configurar laravel API:
	- Entrar al contenedor de php, ejecutamos:
		docker exec -ti laravel-php bash 
	- Estando en sesión bash dentro del contenedor php ejecuatomos:
		composer install
		cd storage/
		mkdir -p framework/{sessions,views,cache}
		chmod -R 777 framework
		cd ..
		php artisan websockets:serve
		exit
	- Movemos archivos de claves:
		docker cp oauth-public.key laravel-php:/app/storage/oauth-public.key
		docker cp oauth-private.key laravel-php:/app/storage/oauth-private.key
	- Validar archivos .env
		En las siguientes rutas deben existir archivos .env
			/heart/.env
			/heart/app/.env	
		En caso de no tener los .env, pedircelos al encargado del proyecto. 					 
5. Validación:
	- entramos desde el navegador a:
		localhost:80  