# PHP MicroFramework

## basic usage
in src/routes.php define app routes:
First will go arguments for injection, and then url arguments.
Also you can create controller class in src/Controller with some action
```php
$router
    ->addRoute(
        '/hello/{name}/{lastname}',
        function(\Artifly\Core\Container $container, \Artifly\Core\TemplateEngine $templateEngine, $name, $lastname) {
            return $templateEngine->render('index.html', ['user' => sprintf('%s %s', $name, $lastname)]);
        }
    )->addRoute(
        '/hello/{name}',
        'DefaultController@indexAction'
    )
;
```

## TODO:
1. ~~Router~~
2. ~~Container with Dependency Injection~~
3. ~~Template Engine~~
4. ~~Refactor file structure in core directory~~
5. ~~Redirect mechanism~~
6. ~~Base controller shorthand functions~~
7. ORM progress: 70%
8. Configuration Manager
9. Migration mechanism