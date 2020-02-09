# AIMUSIC

Una micro applicacion para buscar albums de artistas en Spotify usando SlimFramework

[Repositorio publico](https://github.com/DIOHz0r/aimusic)

## Requisitos

* Necesario PHP 7.1.3 o superior
* [Composer](https://getcomposer.org) .

## Instalación

Clonar el repositorio y ejecutar el siguiente comando en el directorio del proyecto.

```composer install --no-dev``` 

Ejecutar el siguiente comando en el directorio del proyecto

```php -S localhost -t public```

Ingresar porel navegador a la url asignada en el argumento -S del comando anterior ala url de busqueda de artista

```http://localhost/api/v1/albums?q=<artista>```
