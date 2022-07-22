# Cheese Whiz (API Platform)

## About the project

The project is built on top of the Symfony 6 with API Platform 2.6 bundle. Followed by the tutorial of
[SymfonyCasts - API Platform: Serious RESTful APIs](https://symfonycasts.com/screencast/api-platform).

### Build with

![PHP8](https://img.shields.io/static/v1?style=for-the-badge&message=PHP%208&color=777BB4&logoColor=white&logo=php&label=)
![Symfony](https://img.shields.io/static/v1?style=for-the-badge&message=Symfony%206&color=000000&logo=Symfony&logoColor=FFFFFF&label=)
![OpenAPI3](https://img.shields.io/static/v1?style=for-the-badge&message=OpenAPI%203&color=6BA539&logo=OpenAPI+Initiative&logoColor=FFFFFF&label=)

## Getting Started

### Prerequisites

- PHP 8
- Composer 
- Docker-compose

### Installation

1. Clone the repo: 
```bash
git clone https://github.com/bmykyta/cheese-whiz.git
```
2. Copy the .env file and adjust to your needs
3. Run docker compose command
```bash
docker-compose up -d
```
4. Go to php-fpm service 
```bash
docker-compose exec php-fpm bash
```
5. Run composer install, create DB and migrations
```bash
composer intall
php bin/console d:d:c 
php bin/console d:m:m -n
```

