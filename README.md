# REMP Beam Skeleton

This is a pre-configured skeleton of REMP Beam application with simple installation.

Beam Admin serves as a tool for configuration of sites, properties and segments. It's the place to see
the stats based on the tracked events and configuration of user segments.

## Dependencies

To run Beam you have to integrate skeleton application with other REMP tools listed below.

Note: All the dependencies mentioned below are already provided in Docker Compose, so you don't have to install them manually. Use of the Docker Compose and images provided in this repository is not recommended in the production environment and is intended for testing/development purposes.  

### Sso

As a default authentication method for secured routes Beam is using middleware `Remp\LaravelSso\Http\Middleware\VerifyJwtToken`, which authenticates user against preconfigured [SSO service](https://github.com/remp2020/remp/tree/master/Sso) running on `http://sso.remp.press` url.

You can run SSO from the Docker Compose provided in this repository. By default, SSO is exposed at `http://sso.remp.press:9494`. See [docker-compose.override.yml](./docker-compose.override.yml) for more information.

You can also run SSO locally by installing it manually. Please follow [SSO documentation](https://github.com/remp2020/remp/tree/master/Sso). To change the SSO URL edit `.env` variable `REMP_SSO_ADDR`. To properly configure Docker network to access locally-running SSO, you should edit `docker-compose.override.yml` and make your SSO instance accessible to the network of Beam's Docker compose via `extra_hosts` directive:

```yaml
services:
# ... 
  beam:
    extra_hosts:
      - "sso.remp.press:172.17.0.1" # usual IP of the Docker host machine
```

[SSO documentation](https://github.com/remp2020/remp/tree/master/Sso).

### Tracker API

Tracker API is a gateway for storing both user and system events. Tracker validates the request and posts Influx-formatted set of data to a message broker implementation (either Kafka or Pub/Sub).

You can run Tracker API and its dependencies from the Docker Compose provided in this repository. By default, Tracker API is exposed at `http://tracker.remp.press:9494`. See [docker-compose.override.yml](./docker-compose.override.yml) for more information.

If you want to run Tracker API locally, please follow [tracker documentation](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/tracker) and possibly edit configured address of tracker in `.env` configuration `REMP_TRACKER_ADDR`.

[Tracker documentation](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/tracker).

### Segments API

Segments API (also known as Journal API) is read-only API to acquire aggregations over the tracked data.

You can run Segments API and its dependencies (Elasticsearch) from the Docker Compose provided in this repository. By default, Segments API is exposed at `http://segments.remp.press`.

If you want to run Segments API locally, please follow [segments documentation.](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/segments) and edit configured address of tracker in `.env` configuration `REMP_SEGMENTS_ADDR`.

[Segments documentation.](https://github.com/remp2020/remp/tree/master/Beam/go/cmd/segments)

## Installation

### Docker

The simplest possible way is to run this application in Docker containers. Docker Compose is used for orchestrating. Except of these two applications, there is no need to install anything on host machine.

Recommended _(tested)_ versions are:

- [Docker](https://www.docker.com/products/docker-engine) - 24.0.2, build cb74dfc
- [Docker Compose](https://docs.docker.com/compose/overview/) - v2.18.1

#### Steps to install application within docker

1. Get the Beam Skeleton:

    ``` bash
    git clone https://github.com/remp2020/beam-skeleton.git
    ```

    ```bash
    cd beam-skeleton
    ```

2. Prepare environment & configuration files
    ```bash
    # Configuration of Beam web admin
    cp .env.example .env
    ```
    ```bash
    # Configuration of other dependencies Beam requires (databases, other REMP tools)
    cp docker-compose.override.example.yml docker-compose.override.yml
    ```
    
    No changes are required if you want to run application as it is.

    **Note:** Nginx web application runs on the port 9494 by default. Make sure this port is not used, otherwise you will encounter error like this when initializing Docker:

    ```
    ERROR: for nginx  Cannot start service nginx: Ports are not available: listen tcp 0.0.0.0:9494: bind: address already in use
    ```

    In such case, change port mapping in `docker-composer.override.yml`. For example, the following setting maps nginx's internal port 80 to external port 7979, so the application will be available at http://beam.remp.press:7979.

    ```yaml
    services:
    # ...
      nginx:
        ports:
          - "8080:80"
    ```

3. Setup hosts

   Default host used by application is `http://beam.remp.press`. This domain should point to localhost (`127.0.0.1`), so add it to local `/etc/hosts` file.

    ```bash
    echo '127.0.0.1 beam.remp.press' | sudo tee -a /etc/hosts
    echo '127.0.0.1 tracker.remp.press' | sudo tee -a /etc/hosts
    echo '127.0.0.1 segments.remp.press' | sudo tee -a /etc/hosts
    echo '127.0.0.1 sso.remp.press' | sudo tee -a /etc/hosts
    ```

4. Start Docker containers

    ```bash
    docker compose up
    ```

   You should see logs of starting containers. This may include errors, because application was not yet initialized.

5. If you run SSO from the Docker Compose (default), we need to initialize it first. Run the following set of commands:

    ```bash
    # run from anywhere in the project
    docker compose exec mysql mysql -uroot -psecret -e 'CREATE DATABASE IF NOT EXISTS sso'
    docker compose exec sso make install
    docker compose exec sso php artisan key:generate
    docker compose exec sso php artisan jwt:secret
    ```

5. Now we are ready to initialize Beam's web app:

    ```bash
    # run from anywhere in the project
    docker compose exec beam make install
    docker compose exec beam php artisan key:generate
    ```
   
6. Seed the demo data (optional)

    ```bash
    docker compose exec beam php artisan db:seed DemoSeeder
    ```
   
7. Visit `http://beam.remp.press:9494`.

8. Visit testing article to feed data to Beam (optional):

If you seeded demo data (optional step 6), you can visit http://beam.remp.press:9494/test-article.html. The article automatically tracks pageviews and provides sample implementation of API calls that your systems should implement.

First, upsert the information about article to Beam by calling Beam's API in the first green box. Once Beam knows the article information, you should start seeing article stats in the Beam dashboard.

Second, you can track conversion for this article. Each call tracks new conversion to Beam.

#### Updating

When the new version is released, just update Composer (PHP) dependencies and repeat the installation process:

```bash
composer update
make install
```
   
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

Set the application key
```bash
php artisan key:generate
```

#### Updating

When the new version is released, just update Composer (PHP) dependencies and repeat the installation process:

```bash
composer update
make install
```

### Known issues

After the first events are tracked to the Tracker API, the data is not yet readable from the Segments API due to the schema changes. You can see this if the Beam Dashboard doesn't display any data even if it should. The browser's developer tools display message similar to this:

```
Server error: `POST http://segments.remp.press:9494/journal/concurrents/count` resulted in a `500 Internal Server Error` response:\n"elastic: Error 400 (Bad Request): all shards failed [type=search_phase_execution_exception]"\n\n'
```

In order to fix this issue, please restart Segments API, so it can populate it's in-memory cache again.

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

For example see `App\Console\Commands\TestCommand` which can be call by `php artisan test`.

For further information about commands see official [Laravel documentation](https://laravel.com/docs/8.x/artisan).

### Routes

Routes registered by Beam package could be replaced by own routes in files `routes/web.php` for web interface or in `routes/api.php` for API calls. 

Two routes are provided as example. Web route `/test` and API route `/api/test/create`.

For further information about routing see official [Laravel documentation](https://laravel.com/docs/8.x/routing).

### Views

To edit views from Beam package add the own version of view into folder structure `resources/views/vendor/beam/`. Laravel will first check if a custom version of the view has been placed in the folder otherwise will use view from Beam package.

As an example you can see `/resources/views/test/index.blade.php` as the view file for `TestController::index` action.

For further information about views overriding see official [Laravel documentation](https://laravel.com/docs/8.x/packages#overriding-package-views).

### Observers

To listen for model changes (Eloquent events) you can use the observers. New observer can be added by using command `php artisan make:observer ModelNameObserver --model=ModelName` or manually added into folder `app/Observers` and registered in `AppServiceProvider`.

As an example is registered observer `App\Observers\AccountObserver` for model `Remp\BeamModule\Model\Account`. 

For further information about observers overriding see official [Laravel documentation](https://laravel.com/docs/8.x/eloquent#observers).

### Database

Non-breaking database changes you could provide by adding own migrations into folder `database/migrations` or by Artisan command `make:migrations`.

For further information about migrations see official [Laravel documentation](https://laravel.com/docs/8.x/migrations).