# Slender

Slender is a developer-friendly, low-level, rapid web development environment based on Slim framework.

There are no command-line magic tools to manage anything in Slender! You need to create everything manually, by hand.

## Features

- Based on [Slim](https://www.slimframework.com) framework
- Written in MVC architecture
- Has built-in admin panel with [Halfmoon](https://www.gethalfmoon.com) framework
- Has built-in admin and user authentication system
- Has route-based account role management system
- Dependency injection with [PHP DI](https://php-di.org)
- Template management with [Twig](https://twig.symfony.com)
- Database management with [Eloquent](https://laravel.com/docs/8.x/eloquent)
- Email management with [Swift Mailer](https://swiftmailer.symfony.com)
- Date and time manipulation with [Carbon](https://carbon.nesbot.com)
- Image manipulation with [Intervention Image](http://image.intervention.io)
- Markdown manipulation with [Parsedown](https://parsedown.org)
- Data validation with [Rakit Validation](https://github.com/rakit/validation)
- Server-side HTML minify with [HTML Min](https://github.com/voku/HtmlMin)

## Install

You can install Slender by using Composer or Git.

### Using Composer

```
composer create-project enindu/slender <project name>
```

### Using Git

```
git clone https://github.com/enindu/slender.git
```

After installation, you need to rename app configuration file. App configuration file located in `bootstrap/` directory.

```
mv bootstrap/app.php.example bootstrap/app.php
```

Also, make sure you have installed required Composer and npm libraries.

```
composer install && npm install
```

And, make sure you have given directory permission to write.

```
chmod -R 755 cache/
```

Finally, there is one more thing to do before starting the server. You need to create database and configure database connection is app configuration file. For this scenario, you can create database and all the required tables manually or create database and import `sources/database/slender.sql` by using phpMyAdmin.

That's it!
