<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Url\Parse;

/**
 * Class Base
 *
 * @author Enjoys
 */
class BaseParse implements \Enjoys\Route\Url\ParseInterface
{

    protected \Enjoys\Route\Manager $manager;
    protected \Enjoys\Route\Request\RequestInterface $request;

    public function __construct(\Enjoys\Route\Request\RequestInterface $request, \Enjoys\Route\Manager $manager)
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    public function getManager(): \Enjoys\Route\Manager
    {
        return $this->manager;
    }

    public function getRequest(): \Enjoys\Route\Request\RequestInterface
    {
        return $this->request;
    }

    public function parse(): array
    {
        $routeParam = $this->getManager()->getRouteParam();
        
        $route = $this->getRequest()->get($routeParam, $this->getRequest()->getPathInfo());
        $params = $this->getRequest()->get();
        
        unset($params[$routeParam]);
        return ['route' => $route, 'params' => $params];
    }
}
