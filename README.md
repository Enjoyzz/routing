# routing like yii2 
![php 7.4](https://github.com/Enjoyzz/routing/workflows/php%207.4/badge.svg)
![php 8.0](https://github.com/Enjoyzz/routing/workflows/php%208.0/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Enjoyzz/routing/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Enjoyzz/routing/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Enjoyzz/routing/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Enjoyzz/routing/?branch=master)
# make
```php
include __DIR__ . "/vendor/autoload.php";

$request = \Enjoys\Route\Request\Request::createFromGlobals();

$manager = new Enjoys\Route\Manager([
    'baseUrl' => '/sub/',
    'hostInfo' => $request->getHost(),
    'prettyUrl' => true, //or false - default,
    'suffix' => '.html' //work in prettyUrl==true
]);
//also
//$manager->setSuffix('.html');
//also
//$manager->setBaseUrl('/sub/');
//also
//$manager->setHostInfo($request->getHost());

$manager->addRules([
    [
        'pattern' => '',
        'route' => '\Core\Index',
        'suffix' => '/',
    ],
    [
        'pattern' => 'login',
        'route' => '\Core\Signin',
    ],
    [
        'pattern' => 'test',
        'route' => '\Core\Test',
        'suffix' => '.something',
    ],
]);
\Enjoys\Route\URL::setUrlmanager($manager);


\Enjoys\dump(\Enjoys\Route\URL::make(['\Core\\Signin', 'id' => 5], true)); //return http://localhost/sub/login.html?id=5
\Enjoys\dump(\Enjoys\Route\URL::make(['\Core\\Test'], true)); //return http://localhost/sub/test.something
\Enjoys\dump(\Enjoys\Route\URL::make(['\Core\\Index'], false)); //return /sub/

# parse
```apache
RewriteEngine On 
RewriteBase /

RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]

```php
$parser = new \Enjoys\Route\Url\Parse($request, $manager);
\Enjoys\dump($parser);

//return
//Enjoys\Route\Url\Parse::__set_state(array(
//   'result' => 
//  array (
//    'rule' => 'login',
//    'route' => '\Core\Signin',
//    'params' => 
//    array (
//    ),
//  ),
//))
