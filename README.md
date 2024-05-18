Symfony Dockerized Application

Setup:

1. Create the environment configuration:

   `cp _env .env`

2. Build and start the containers:
   `docker compose build --no-cache`
   `docker compose up -d`

Usage:

1. Calculate commissions from data file:

  `docker compose run php php bin/console app:calculate-commissions /data/input.txt`

2. Run unit tests:

  `docker compose run php php vendor/bin/phpunit`

3. Show defined areas and their countries:

  `docker compose run php php bin/console app:show-areas`

4. Add a country to an area:

  `docker compose run php php bin/console app:add-country GB, NON_EU`