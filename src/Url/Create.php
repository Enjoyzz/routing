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
    use \Enjoys\Route\Traits\Manager;

    private string $route;
    private array $params;

    private const BASE_CLASS = Create\BaseCreate::class;
    private const RULE_CLASS = Create\RuleCreate::class;

    public function __construct(string $route, array $params, \Enjoys\Route\Manager $manager)
    {
        $this->manager = $manager;
        $this->route = $route;
        $this->params = $params;
    }

    public function returnUrl(): string
    {
        $class = self::BASE_CLASS;

        if ($this->getManager()->getOption('prettyUrl')) {
            $class = self::RULE_CLASS;
        }

        return (new $class($this->route, $this->params, $this->getManager()))->returnUrl();
    }

    public function createAbsoluteUrl(string $relativeUrl, $scheme = true): string
    {
        if (!\Enjoys\Route\Helpers::isRelative($relativeUrl)) {
            return $relativeUrl;
        }

        $hostInfo = $this->getManager()->getHostInfo();
        $url = $hostInfo . $relativeUrl;

        if (strncmp($relativeUrl, '//', 2) === 0) {
            $pos = strpos($hostInfo, '://');
            if ($pos !== false) {
                $url = substr($hostInfo, 0, $pos) . ':' . $relativeUrl;
            }
        }

        return \Enjoys\Route\Helpers::ensureScheme($url, $scheme);
    }
}
