<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Test\Enjoys\Route\Url;

/**
 * Class ParseTest
 *
 * @author Enjoys
 */
class ParseTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider data_1
     */
    public function test_1($url, $excpect_route, $expect_params)
    {
        $request = \Enjoys\Route\Request\Request::create($url);

        $urlManager = new \Enjoys\Route\Manager([
            'baseUrl' => '/',
            'hostInfo' => 'http://localhost',
            'prettyUrl' => true
        ]);
        $urlManager->addRules(require(__DIR__ . '/../fixtures/dinamic_route.php'));
        $parsedRequest = new \Enjoys\Route\Url\Parse($request, $urlManager);

        $this->assertSame($excpect_route, $parsedRequest->getRoute());
        $this->assertSame($expect_params, $parsedRequest->getParams());
    }

    public function data_1()
    {
        return [
            ['http://localhost/Band/Song.html@lesson_100', '\Song\Lesson', ['band' => 'Band', 'song' => 'Song', 'data_id' => '100']],
            ['http://localhost/Band/Song.html@leSSon_100', '\Song\Lesson', ['band' => 'Band', 'song' => 'Song', 'data_id' => '100']],
            ['/Band/Song.html@rAnDoM_100', '\Song\Random', ['band' => 'Band', 'song' => 'Song', 'data_id' => '100']],
            ['/Band/Song.html@lesson_100', '\Song\Lesson', ['band' => 'Band', 'song' => 'Song', 'data_id' => '100']],
            ['/admin/post/news/create', '\news\PostCreate', []],
            ['/admin/rAnDoM/mOdUlle/create', '\mOdUlle\RandomCreate', []],
            ['/anything?test=5', '/anything', ['test' => '5']],
        ];
    }

    /**
     * @dataProvider data_2
     */
    public function test_2($baseUrl, $prettyUrl, $url, $excpect_route, $expect_params)
    {
        $request = \Enjoys\Route\Request\Request::create($url);

        $urlManager = new \Enjoys\Route\Manager([
            'baseUrl' => $baseUrl,
            'hostInfo' => 'http://localhost',
            'prettyUrl' => $prettyUrl
        ]);
        $urlManager->addRules(require(__DIR__ . '/../fixtures/variable_rules.php'));
        $parsedRequest = new \Enjoys\Route\Url\Parse($request, $urlManager);

        $this->assertSame($excpect_route, $parsedRequest->getRoute());
        $this->assertSame($expect_params, $parsedRequest->getParams());
    }

    public function data_2()
    {
        return [
            ['/', false, '/?route=Digits&id=2', 'Digits', ['id' => '2']],
            ['/', true, '/?route=Digits&id=2', 'Digits', ['id' => '2']],
            ['/', true, '/digits/1.html', 'Digits', ['id' => '1']],
//            ['/', true, '/encode_f/Привет', 'Encode\False', ['text' => 'Привет']],
//            ['/', true, '/encode/%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82', 'Encode\True', ['text' => '%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82']],
//            ['/', true, '/encode/%5B%5D', 'Encode\True', ['text' => '%5B%5D']],
            ['/', true, '/search/', 'Search', []],
            
        ];
    }
}
