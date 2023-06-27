# REMP Beam Skeleton

This is a pre-configured skeleton of REMP Beam application with simple installation.

Beam Admin serves as a tool for configuration of sites, properties and segments. It's the place to see
the stats based on the tracked events and configuration of user segments.

## Installation

### Docker

The simplest possible way is to run this application in docker containers. Docker Compose is used for orchestrating. Except of these two application, there is no need to install anything on host machine.

Recommended _(tested)_ versions are:

- [Docker](https://www.docker.com/products/docker-engine) - 20.10.24, build 297e128
- [Docker Compose](https://docs.docker.com/compose/overview/) - 1.29.2, build 5becea4c

#### Steps to install application within docker

1. Get the application

    ``` bash
    git clone https://github.com/remp2020/beam-skeleton.git
    ```

    ```bash
    cd beam-skeleton
    ```

2. Prepare environment & configuration files
    ```bash
    cp .env.example .env
    ```
    ```bash
    cp docker-compose.override.example.yml docker-compose.override.yml
    ```
   No changes are required if you want to run application as it is.

   **Note:** nginx web application runs on the port 80. Make sure this port is not used, otherwise you will encounter error like this when initializing Docker:

    ```
    ERROR: for nginx  Cannot start service nginx: Ports are not available: listen tcp 0.0.0.0:80: bind: address already in use
    ```

   In such case, change port mapping in `docker-composer.override.yml`. For example, the following setting maps internal port 80 to external port 8080, so the application will be available at http://beam.press:8080.
    ```yaml
    services:
    # ...
      nginx:
        ports:
          - "8080:80"
    ```

3. Setup host

   Default host used by application is `http://beam.press`.
   This domain should by pointing to localhost (`127.0.0.1`), so add it to local `/etc/hosts` file.

    ```bash
    echo '127.0.0.1 beam.press' | sudo tee -a /etc/hosts
    ```

4. Start Docker containers

    ```bash
    docker compose up
    ```

   You should see logs of starting containers. This may include errors, because application was not yet initialized.
   Enter the application docker container:

    ```bash
    # run from anywhere in the project
    docker compose exec beam bash
    ```

   When inside the container, add required permissions:

    ```bash
    chmod -R a+rw storage
    ```

   After that, choose and run one of the two installation options:

      ```bash
      make install
      ```

5. Integration with other REMP services:

   #### Sso
   
   As a default authentication method for secured routes Beam is using middleware `Remp\LaravelSso\Http\Middleware\VerifyJwtToken`, which authenticates user against preconfigured [SSO service](https://github.com/remp2020/remp/tree/master/Sso) running on `http://sso.remp.press` url.
   To change Sso url edit `.env` variable `REMP_SSO_ADDR`.

   To proper network configuration you should edit `docker-compose.override.yml` according your setup:

   ```yaml
   services:
   # ... 
     beam:
       extra_hosts:
         - "sso.remp.press:192.168.65.2"
   ```

   [Sso documentation](https://github.com/remp2020/remp/tree/master/Sso).

   #### Segments

   Segments (also known as Journal) is read-only API to acquire aggregations over the tracked data.
   
   To run segments please see [segments documentation.](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/segments)

   Now edit configured address of segments service in `.env` configuration `REMP_SEGMENTS_ADDR`.

   #### Tracker

   Tracker is a gateway for storing both user and system events. Tracker validates the request and posts Influx-formatted set of data to a message broker implementation (either Kafka or Pub/Sub).
   
   To run tracker locally please follow [tracker documentation](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/tracker).
   
   Now edit configured address of tracker in `.env` configuration `REMP_TRACKER_ADDR`.

   [Tracker documentation](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/tracker).

### Manual installation

#### Dependencies

- PHP 8.1
- MySQL 8
- Redis 6.2

#### Installation

[comment]: <> (Clone this repository and run the following command inside the project folder:)
Clone this repository, go inside the folder and run the following to create configuration files from the sample ones:

```bash
cp .env.example .env
```

Edit `.env` file and set up all required values such as database and Redis connections.

Now run the installation:

```bash
make install
```

## Customization

Beam-skeleton is Laravel application with standard [directory structure](https://laravel.com/docs/8.x/structure) and whole Beam functionality is provided as [Laravel package](https://laravel.com/docs/8.x/packages).
[Beam-module](https://github.com/remp2020/beam-module) provides own routes, commands, UI, database migrations.

So for the further information please follow official [Laravel documentation](https://laravel.com/docs/8.x) on corresponding version.

### Configuration

All of the configuration files for the Laravel framework are stored in the `config` directory. Also configuration files from Beam package are published into config folder during project initialization.
Most configuration values could be overwritten in [environment](https://laravel.com/docs/8.x/configuration#environment-configuration) `.env` file.

For further information about configuration see official [Laravel documentation](https://laravel.com/docs/8.x/configuration).

### Commands

Along the commands provided by Beam package you could add own commands by manual adding into folder `app/Console/Commands/` or by Artisan command `make:command`. If you would like to change the behaviour of Beam command you could register own version of command with the same signature. 

For further information about commands see official [Laravel documentation](https://laravel.com/docs/8.x/artisan).

### Routes

Routes registered by Beam package could be replaced by own routes in files `routes/web.php` for web interface or in `routes/api.php` for API calls. 

For further information about routing see official [Laravel documentation](https://laravel.com/docs/8.x/routing).

### Views

To edit views from Beam package add the own version of view into folder structure `resources/views/vendor/beam/`. Laravel will first check if a custom version of the view has been placed in the folder otherwise will use view from Beam package.

For further information about views overriding see official [Laravel documentation](https://laravel.com/docs/8.x/packages#overriding-package-views).

### Database

Non-breaking database changes you could provide by adding own migrations into folder `database/migrations` or by Artisan command `make:migrations`.

For further information about migrations see official [Laravel documentation](https://laravel.com/docs/8.x/migrations).