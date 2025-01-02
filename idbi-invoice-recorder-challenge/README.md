# IDBI Invoice Recorder Challenge

API REST que permite registrar comprobantes en formato XML y consultarlos. A partir de estos comprobantes, se extrae
información relevante como los datos del emisor y receptor, los artículos o líneas incluidas y los montos totales.

La API utiliza JSON Web Token (JWT) para la autenticación.

## Componentes

El proyecto se ha desarrollado utilizando las siguientes tecnologías:

- PHP
- Nginx (servidor web)
- MySQL (base de datos)
- MailHog (gestión de envío de correos)

## Preparación del Entorno

El proyecto cuenta con una implementación de Docker Compose para facilitar la configuración del entorno de desarrollo.

> ⚠️ Si no estás familiarizado con Docker, puedes optar por otra configuración para preparar tu entorno. Si decides
> hacerlo, omite los pasos 1 y 2.

Instrucciones para iniciar el proyecto

1. Levantar los contenedores con Docker Compose:

```bash
docker compose up -d
```

2. Acceder al contenedor web:

```bash
docker exec -it idbi-invoice-recorder-challenge-web-1 bash
```

3. Configurar las variables de entorno:

```bash
cp .env.example .env
```

4. Configurar el secreto de JWT en las variables de entorno (genera una cadena de texto aleatoria):

```bash
JWT_SECRET=<random_string>
```

5. Instalar las dependencias del proyecto:

```bash
composer install
```

6. Generar una clave para la aplicación:

```bash
php artisan key:generate
```

7. Ejecutar las migraciones de la base de datos:

```bash
php artisan migrate
```

8. Rellenar la base de datos con datos iniciales:

```bash
php artisan db:seed
```

**¡Y listo!** Ahora puedes empezar a desarrollar.

## Uso

La API estará disponible en: http://localhost:8080/api/v1

### Gestión de Correos

Para visualizar los correos enviados por la aplicación, puedes acceder a la interfaz de MailHog desde tu navegador
en: http://localhost:8025

## Nuevas consideraciones

#### Almacenamiento de Información Adicional en Comprobantes

- Se aplicaron los nuevos campos serie, numero, tipo y moneda para la tabla de vouchers

#### Procesamiento Asíncrono de Comprobantes

- Se cambio la QUEUE_CONNECTION a database para que funcione de manera asincrona

- También se creo el job para el procesamiento del voucher y el envio de la notificacion.

#### Consulta de Montos Totales Acumulados por Moneda

- Se creo un endpoint llamado 'montos' para el procesamiento de los montos totales que realizo el usuario en soles y dolares de los vouchers que haya registrado.
- GET - Para la visualizacion de montos en soles y dolares: http://localhost:8080/api/v1/montos

#### Eliminación de Comprobantes por Identificador

- Se creo el nuevo endpoint DELETE para la eliminación de un voucher.
- GET Para la visualizacion de montos en soles y dolares: http://localhost:8080/api/v1/montos


#### Filtros Avanzados en la Consulta de Comprobantes

- Se aplicaron filtros avanzados al endpoint existente GET de vouchers, siendo el principal y requerido la fecha de inicio y fin-

- DELETE - Para eliminar un voucher (el usuario que lo elimine tiene que estar autenticado como el usuario creador de ese voucher) http://localhost:8080/api/v1/vouchers/9dde9eae-55c7-4738-a16a-2311fa6a536a

