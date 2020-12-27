# Slender

Rapid web development environment based on Slim framework

## Features

- Based on [slim/slim](https://www.slimframework.com)
- Manage views with [twig/twig](https://twig.symfony.com)
- Manage database with [illuminate/database](https://laravel.com/docs/8.x/database)
- Inject dependencies with [php-di/php-di](https://php-di.org)
- Manage emails with [swiftmailer/swiftmailer](https://swiftmailer.symfony.com)
- Manipulate date and time with [nesbot/carbon](https://carbon.nesbot.com)
- Manage filesystem with [symfony/filesystem](https://github.com/symfony/filesystem)
- Validate data with [rakit/validation](https://github.com/rakit/validation)
- Manipulate images with [intervention/image](https://github.com/Intervention/image)

## Install

You can install Slender by using Composer or clone the repository by using Git.

### Use Composer

```
$ composer create-project enindu/slender <project name>
```

### Use Git

```
$ git clone https://github.com/enindu/slender.git
```

### Install Required Libraries

After installation, you may need to install required Composer and npm libraries.

```
$ composer install
$ npm install
```

### Change Directory Permissions

And also, you may need to set permissions to write.

```
# chmod -R 755 cache/
```
