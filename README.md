## Copy _env to .env
and start hacking!

`docker compose build --no-cache`
`docker compose up -d`

Main task:
`docker compose run php php bin/console app:calculate-commissions /data/input.txt`

Run tests:
`docker compose run php php vendor/bin/phpunit`

Additional work done for handling countries and zones easy way:
`docker compose run php php bin/console app:show-areas`

Add country to an Area, example:
`docker compose run php php bin/console app:add-country GB, NON_EU`
