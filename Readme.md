[![<zawiszaty>](https://circleci.com/gh/zawiszaty/entitlement_service.svg?style=svg)](https://circleci.com/gh/zawiszaty/entitlement_service)


# Entitlement service

## How to run 
You must have installed docker and docker-compose locally.

### Windows
```bash
~ docker-compose up -d
//wait some time
~ docker-compose exec -it  php ./vendor/bin/phpunit
```

### Linux
```bash
~ make start
~ make test
```