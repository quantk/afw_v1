# PHP MicroFramework

## basic usage
in src/routes.php define app routes:
First will go arguments for injection, and then url arguments.
Also you can create controller class in src/Controller with some action
### Router
```php
$router
    ->addRoute(
        '/hello/{name}/{lastname}',
        function(\Artifly\Core\Container $container, \Artifly\Core\TemplateEngine $templateEngine, $name, $lastname) {
            return $templateEngine->render('index.html', ['user' => sprintf('%s %s', $name, $lastname)]);
        }
    )->addRoute(
        '/hello/{name}',
        'DefaultController@indexAction',
        [Route::POST_METHOD, Route::GET_METHOD, Route::PUT_METHOD, Route::DELETE_METHOD]
    )
;
```

### ORM
```php
    $em = $container->get(\Artifly\Core\Component\ORM\EntityManager::class);
    // Create 
    // User = plain php object with fields and getters/setters
    $user = new User();
    $user->setName('Name');
    $user->setLastName('LastName');
    $em->save($user);
    
    // Find
    $user = $em->find(\App\Model\User::class, 5);
    // Collection by args and order
    $user = $em->findBy(\App\Model\User::class, ['name' => 'Name'], ['name' => 'DESC']);
    // One entity by args and order
    $user = $em->findOneBy(\App\Model\User::class, ['name' => 'Name'], ['name' => 'DESC']);
    
    
    // Update
    $user = $em->find(\App\Model\User::class, 5);
    $user->setName('Another name');
    $em->save($user);
    
    // Raw Query
    // Return filled User Objects
    $result = $em->executeRaw(
        'SELECT * FROM users',
        \App\Model\User::class
    );
    
```

## TODO:
1.  ~~Router~~
2.  ~~Container with Dependency Injection~~
3.  ~~Template Engine~~
4.  ~~Refactor file structure in core directory~~
5.  ~~Redirect mechanism~~
6.  ~~Base controller shorthand functions~~
7.  ORM progress: 90%
8.  ~~Logger~~
9.  ~~Create test~~
10.  Event System
11. ~~Configuration Manager~~
12. Auth system
13. Migration mechanism
14. Console commands