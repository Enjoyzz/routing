<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Url;

/**
 * Class Create
 *
 * @author Enjoys
 */
class Create
{

    private \Enjoys\Route\Manager $manager;
    private string $route;
    private array $params;

    private const BASE_CLASS = CreateFactory\Base::class;
    private const RULE_CLASS = CreateFactory\Rule::class;

    public function __construct(string $route, array $params, \Enjoys\Route\Manager $manager)
    {
        $this->manager = $manager;
        $this->route = $route;
        $this->params = $params;
    }

    public function returnUrl()
    {
        $class = self::BASE_CLASS;

        if ($this->manager->getOption('prettyUrl')) {
            $class = self::RULE_CLASS;
        }

        return (new $class($this->route, $this->params, $this->manager))->returnUrl();
    }

    public function createAbsoluteUrl(string $relativeUrl): string
    {
        if (!$this->isRelative($relativeUrl)) {
            return $relativeUrl;
        }

        $hostInfo = $this->manager->getHostInfo();
        $url = $hostInfo . $relativeUrl;
        
        if (strncmp($relativeUrl, '//', 2) === 0) {
            $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $relativeUrl;
        }

        return $url;
    }

    private function isRelative(string $url): bool
    {
        return strncmp($url, '//', 2) && strpos($url, '://') === false;
    }
}
