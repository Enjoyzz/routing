<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Url\CreateFactory;

/**
 * Class Base
 *
 * @author Enjoys
 */
class Base implements \Enjoys\Route\Url\CreateInterface
{

    protected \Enjoys\Route\Manager $manager;
    protected string $route;
    protected array $params;
    protected string $anchor;
    protected string $baseUrl;

    public function __construct(string $route, array $params, \Enjoys\Route\Manager $manager)
    {
        $this->manager = $manager;
        $this->route = $route;
        $this->params = $params;
       

        $this->baseUrl = $this->getManager()->getBaseUrl();
        $this->anchor = isset($params['#']) ? '#' . $params['#'] : '';

        unset($this->params['#'], $this->params[$this->getManager()->getRouteParam()]);
    }

    public function getManager()
    {
        return $this->manager;
    }

    function returnUrl(): string
    {
        $url = "$this->baseUrl?{$this->getManager()->getRouteParam()}=" . urlencode($this->route);
        if (!empty($this->params) && ($query = http_build_query($this->params)) !== '') {
            $url .= '&' . $query;
        }
        return $url . $this->anchor;
    }
}
