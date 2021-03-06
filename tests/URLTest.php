<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Tests\Enjoys\Route;

/**
 * Class URLTest
 *
 * @author Enjoys
 */
class URLTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider data_test_1
     */
    public function test_1($url, $scheme, $expect)
    {
        $urlManager = new \Enjoys\Route\Manager([
            'baseUrl' => '/',
            'hostInfo' => 'http://localhost',
            'routeParam' => 'r',
        ]);
        \Enjoys\Route\URL::setUrlmanager($urlManager);

        $buildedUrl = \Enjoys\Route\URL::make($url, $scheme);
        $this->assertSame($expect, urldecode($buildedUrl));
    }

    public function data_test_1()
    {
        return [
            [['Route\\Conrete', 'id' => 5], false, '/?r=Route\Conrete&id=5'],
            [['Route\\Conrete', 'id' => 5], true, 'http://localhost/?r=Route\Conrete&id=5'],
        ];
    }

    /**
     * @dataProvider data_test_2
     */
    public function test_2($baseUrl, $url, $scheme, $expect)
    {
        $urlManager = new \Enjoys\Route\Manager([
            'baseUrl' => $baseUrl,
            'hostInfo' => 'http://localhost',
            'prettyUrl' => true,
        ]);
        $urlManager->addRules(require(__DIR__ . '/fixtures/simple_rules.php'));

        \Enjoys\Route\URL::setUrlmanager($urlManager);

        $buildedUrl = \Enjoys\Route\URL::make($url, $scheme);
        $this->assertSame($expect, urldecode($buildedUrl));
    }

    public function data_test_2()
    {
        return [
            ['/', ['\Core\Signin', 'id' => 5], false, '/login?id=5'],
            ['/', ['\\Core\\Signin', 'id' => 5], false, '/login?id=5'],
            ['/', ['\Core\\Signin', 'id' => 5], false, '/login?id=5'],
            ['/sub/', ['\Core\\Signin', 'id' => 5], false, '/sub/login?id=5'],
            ['/sub/', ['\Core\\Signin', 'id' => 5], true, 'http://localhost/sub/login?id=5'],
            ['/sub', ['\Core\\Signin', 'id' => 5], false, '/sub/login?id=5'],
            ['/sub', ['\Core\\Signin', 'id' => 5], true, 'http://localhost/sub/login?id=5'],
            ['sub', ['\Core\\Signin', 'id' => 5], false, '/sub/login?id=5'],
            ['sub', ['\Core\\Signin', 'id' => 5], true, 'http://localhost/sub/login?id=5'],
            ['sub/', ['\Core\\Signin', 'id' => 5], true, 'http://localhost/sub/login?id=5'],
            ['/', ['Route\\Conrete', 'id' => 5], true, 'http://localhost/?route=Route\Conrete&id=5'],
            ['/', ['\Core\Index'], false, '/'],
            ['/test', ['\Core\Index'], false, '/test/'],
            ['/test', ['\Core\Index'], true, 'http://localhost/test/'],
        ];
    }

    /**
     * @dataProvider data_test_3
     */
    public function test_3($baseUrl, $prettyUrl, $url, $scheme, $expect)
    {
        $urlManager = new \Enjoys\Route\Manager([
            'baseUrl' => $baseUrl,
            'hostInfo' => 'http://localhost',
            'prettyUrl' => $prettyUrl,
        ]);
        $urlManager->addRules(require(__DIR__ . '/fixtures/variable_rules.php'));

        \Enjoys\Route\URL::setUrlmanager($urlManager);

        $buildedUrl = \Enjoys\Route\URL::make($url, $scheme);
        $this->assertSame($expect, $buildedUrl);
    }

    public function data_test_3()
    {
        return [
            ['/', false, ['Books', 'cat_id' => 5, 'sort' => 'asc'], false, '/?route=Books&cat_id=5&sort=asc'],
            ['/', true, ['Books', 'cat_id' => 5, 'sort' => 'asc'], false, '/books/5/?sort=asc'],
            ['/', false, ['Music', 'category' => 'Rock', 'sort' => 'asc'], false, '/?route=Music&category=Rock&sort=asc'],
            ['/', true, ['Music', 'category' => 'Rock', 'sort' => 'asc'], false, '/musics/Rock/asc/'],
            ['/', true, ['Music', 'category' => 'Rock', 'sort' => 'invalid'], false, '/?route=Music&sort=invalid'],
            ['/', true, ['Music', 'category' => 'Rock'], false, '/musics/Rock/desc/'],
            ['/', true, ['Digits'], false, '/digits/1.html'],
            ['/sub', true, ['Digits', 'id' => 65], false, '/sub/digits/65.html'],
            ['/', true, ['Digits', 'text' => 'Привет'], false, '/digits/1.html?text=%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82'],
            ['/', true, ['Encode\True', 'text' => 'Привет'], false, '/encode/%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82'],
            ['/', true, ['Encode\False', 'text' => 'Привет'], false, '/encode_f/Привет'],
            ['/', true, ['Search', 'text' => 'Привет'], false, 'http://yandex.ru/s/?text=%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82'],
            ['/', false, ['Search', 'text' => 'Привет'], false, '/?route=Search&text=%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82'],
        ];
    }

    /**
     * @dataProvider data_test_4
     */
    public function test_4($baseUrl, $prettyUrl, $url, $scheme, $expect)
    {
        $urlManager = new \Enjoys\Route\Manager([
            'baseUrl' => $baseUrl,
            'hostInfo' => 'http://localhost',
            'prettyUrl' => $prettyUrl,
        ]);
        $urlManager->addRules(require(__DIR__ . '/fixtures/dinamic_route.php'));

        \Enjoys\Route\URL::setUrlmanager($urlManager);

        $buildedUrl = \Enjoys\Route\URL::make($url, $scheme);
        $this->assertSame($expect, $buildedUrl);
    }

    public function data_test_4()
    {
        return [
            ['/', true, ['<controller>\view', 'controller' => 'post', 'id' => 1], false, '/post/1/'],
            ['/', true, ['<controller>\view', 'controller' => 'comment', 'id' => 1], false, '/comment/1/'],
            ['/', true, ['<class>\<action>', 'class' => 'comment', 'action' => 'view5'], false, '/comment/view5/55/'],
            ['/', true, ['<class>\<action>', 'class' => 'comment', 'action' => 'view5'], false, '/comment/view5/55/'],
            // ['/', true, ['\Song\<action>', 'action' => 'A', 'band' => 'B', 'song' => 'S', 'data_id' => 1], false, '/B/S.html@A_1'],
            // ['/', true, ['\Song\<action>', 'action' => 'AbC', 'band' => 'B', 'song' => 'S', 'data_id' => 1], false, '/B/S.html@AbC_1'],
            ['/', true, ['\chords\Core\Song\<act>', 'act' => 'aBC', 'band' => 'B', 'song' => 'S', 'id' => 1], false, '/B/S@aBC_1.php'],
        ];
    }
}
