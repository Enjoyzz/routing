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
class Base implements \Enjoys\Route\Url\ParseInterface
{

    protected \Enjoys\Route\Manager $manager;
    protected \Enjoys\Route\Request\RequestInterface $request;

    public function __construct(\Enjoys\Route\Request\RequestInterface $request, \Enjoys\Route\Manager $manager)
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function parse()
    {
        $routeParam = $this->getManager()->getRouteParam();
        $route = $this->request->get($routeParam, $this->request->getPathInfo());
        $params = $this->request->query->all();
        unset($params[$routeParam]);
        return ['route' => $route, 'params' => $params];
    }
}
