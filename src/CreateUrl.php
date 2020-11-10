<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route;

/**
 * Class CreateUrl
 *
 * @author Enjoys
 */
class CreateUrl
{

    private Manager $manager;
    private string $route;
    private array $params;
    private string $anchor;
    private string $baseUrl;

    public function __construct(string $route, array $params, \Enjoys\Route\Manager $manager)
    {
        $this->manager = $manager;
        $this->route = $route;
        $this->params = $params;

        $this->baseUrl = $this->getManager()->getBaseUrl();
        $this->anchor = isset($params['#']) ? '#' . $params['#'] : '';
    }

    public function returnUrl(): string
    {
        unset($this->params['#'], $this->params[$this->getManager()->getRouteParam()]);
        if ($this->getManager()->getOption('prettyUrl')) {
            foreach ($this->getManager()->getRules() as $rule) {
                $url = $this->createFromRule($rule);
                if ($url !== false) {
                    break;
                }
            }
            return "$this->baseUrl/{$url}{$this->anchor}";
        }
    }

    private function createFromRule(Rule $rule)
    {
        $tr = [];
        if ($this->route !== $rule->route) {

            if ($rule->_routeRule !== null && preg_match($rule->_routeRule, $this->route, $matches)) {

                $matches = $this->substitutePlaceholderNames($matches);
                foreach ($rule->_routeParams as $name => $token) {
                    if (isset($rule->defaults[$name]) && strcmp($rule->defaults[$name], $matches[$name]) === 0) {
                        $tr[$token] = '';
                    } else {
                        $tr[$token] = $matches[$name];
                    }
                }
            } else {
                // $this->createStatus = 1;
                return false;
            }
        }

        foreach ($rule->_paramRules as $name => $_rule) {
            if (isset($params[$name]) && !is_array($params[$name]) && ($_rule === '' || preg_match($_rule, $params[$name]))) {
                $tr["<$name>"] = $rule->encodeParams ? urlencode($params[$name]) : $params[$name];
                unset($params[$name]);
            } elseif (!isset($rule->defaults[$name]) || isset($params[$name])) {

                return false;
            }
        }
        $url = $this->trimSlashes(strtr($rule->_template, $tr));

        if ($this->host !== null) {
            $pos = strpos($url, '/', 8);
            if ($pos !== false) {
                $url = substr($url, 0, $pos) . preg_replace('#/+#', '/', substr($url, $pos));
            }
        } elseif (strpos($url, '//') !== false) {
            $url = preg_replace('#/+#', '/', trim($url, '/'));
        }
        if ($url !== '') {
            $url .= ($rule->suffix === null ? $manager->suffix : $rule->suffix);
        }
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '?' . $query;
        }
        return $url;
    }

    public function createAbsoluteUrl(string $url)
    {

        if (strpos($url, '://') === false) {
            $hostInfo = $this->getManager();
            if (strncmp($url, '//', 2) === 0) {
                $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
            } else {
                $url = $hostInfo . $url;
            }
        }
        return $url;
    }

    public function getManager()
    {
        return $this->manager;
    }
    
        private function trimSlashes($string)
    {
        if (strncmp($string, '//', 2) === 0) {
            return '//' . trim($string, '/');
        }
        return trim($string, '/');
    }
}
