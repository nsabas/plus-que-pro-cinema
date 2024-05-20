# Plus que pro cinema

Plus que pro cinema is a backoffice that allow movie trend management.

## Prerequisite

- PHP 8.2
- Composer 2
- Yarn
- Docker

## Installation

It use docker multistage, so we do have to build both stage and run them to init system.

1. Build:
``` sh
$ docker-compose build
```

2. Run:
``` sh
$ docker-compose up -d
```
## Usage

After installation, you can run the backoffice on [localhost:8000/admin](http://localhost:8000/admin)

### Credentials

admin@admin.fr / admintest
