## Create dotenv file

Create a file named `.env` within the `app/` directory.

## Explaining the directory structure

In the future this will be automated, but currently you have to define
the application structure by yourself.

```bash
app/
  bin/
    console
  config/
    app.conf.php
    routes.conf.php
  logs/
  public/
  src/
  templates/
  .env
```

**Let's go through this step by step.**

### `bin/`

In this directory lives the `console` application. This file also has to be created
by you. Create a file named `console` (without any extensions) and put the following
content in there:

```php
#!/usr/bin/php

<?php

require __DIR__ . '/../vendor/autoload.php';

[$config, $entityManager, $logger, $container] = \Faulancer\Kernel::bootDefaults();

$console = new \Faulancer\Console($container);
$console->setConfig($config);
$console->setEntityManager($entityManager);
$console->setLogger($logger);

$console->handle($argv);
```

### `config/`

Currently there are two files neccessary. The `app.conf.php` and the `routes.conf.php`.

```php
<?php

// app.conf.php

return [
    'logs' => [
        'path' => __DIR__ . '/../logs/',
        'minLevel' => 'ENV:LOG_MIN_LEVEL'
    ],
    'templates' => [
        'path' => __DIR__ . '/../templates'
    ],
    'database' => [
        'user' => 'ENV:MYSQL_USER',
        'pass' => 'ENV:MYSQL_PASS',
        'db'   => 'ENV:MYSQL_DB',
        'host' => 'ENV:MYSQL_HOST'
    ],
    'translation' => [
        'path' => __DIR__ . '/../translations/'
    ]
];

```

```php
<?php

// routes.conf.php

return [
    'home' => [
        'path' => '/',
        'class' => \App\Controller\ExampleController::class,
        'action' => 'example'
    ]
];

```

### `logs/`

The logs folder is the home of our useful hints when something isn't
working the way it is expected. The whole framework writes logs for various
events, so there is mostly always some hint in there.

When the Kernel is booted, the log files for the specific environment are
also created automatically if there aren't any present.

### `public/`

This is the public part of your application. Everything which should be
publicly visible, should be put in there. This goes from assets like CSS, JavaScript
and Images to even more specific ones like search engine validation scripts.

### `src/`

When you're asking yourself, where does my business logic live, this is the place to be.
In there is everything beginning from the Controllers over to the ViewHelpers.

### `templates/`

Here you're able to put in your templates. More on this topic in the Templates section.