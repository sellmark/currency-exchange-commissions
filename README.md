## How to run in few steps

1. Copy _env to .env
`docker compose build --no-cache`
`docker compose up -d`

2. Main task:
`docker compose run php php bin/console app:calculate-commissions /data/input.txt`

3. Run tests:
`docker compose run php php vendor/bin/phpunit`

4. Additional work done for handling countries and zones easy way:
`docker compose run php php bin/console app:show-areas`

5. Add country to an Area, example:
`docker compose run php php bin/console app:add-country GB, NON_EU`
