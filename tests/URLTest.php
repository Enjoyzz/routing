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
                //'routeParam' => 'r',
        ]);
        \Enjoys\Route\URL::setUrlmanager($urlManager);
        
        $buildedUrl = \Enjoys\Route\URL::make($url, $scheme);
        $this->assertSame($expect, urldecode($buildedUrl));
    }
    
    public function data_test_1()
    {
        return [
            [['Route\\Conrete', 'id' => 5], false, '/?route=Route\Conrete&id=5'],
            [['Route\\Conrete', 'id' => 5], true, 'http://localhost/?route=Route\Conrete&id=5'],
        ];
    }
}
