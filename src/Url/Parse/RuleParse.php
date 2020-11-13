<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Url\Parse;

/**
 * Class Rule
 *
 * @author Enjoys
 */
class RuleParse extends BaseParse implements \Enjoys\Route\Url\ParseInterface
{

    public function parse()
    {
        foreach ($this->getManager()->getRules() as $rule) {

            $result = $this->parseRequest($rule);

            if ($result !== false) {
                return $result;
            }
        }

        if ($this->getManager()->getOption('enableStrictParsing')) {
            return false;
        }

        return parent::parse();
    }

    private function parseRequest(\Enjoys\Route\Rule $rule)
    {
//        if ($this->mode === self::CREATION_ONLY) {
//            return false;
//        }
//\Enjoys\dump($rule);
        if (!empty($rule->verb) && !in_array($this->getRequest()->getMethod(), $rule->verb, true)) {
            return false;
        }

        $suffix = (string) ($rule->suffix === null ? $this->getManager()->suffix : $rule->suffix);

        $pathInfo = ltrim($this->getRequest()->getPathInfo(), '/');


        if ($suffix !== '' && $pathInfo !== '') {
            $n = strlen($suffix);
            if (substr_compare($pathInfo, $suffix, -$n, $n) === 0) {
                $pathInfo = substr($pathInfo, 0, -$n);

                if ($pathInfo === '') {
                    // suffix alone is not allowed
                    return false;
                }
            } else {
                return false;
            }
        }
        if ($rule->host !== null) {
            $pathInfo = ltrim(strtolower($this->getRequest()->getHost()) . ($pathInfo === '' ? '' : '/' . $pathInfo), '/');
        }

        if (!preg_match($rule->pattern, $pathInfo, $matches)) {
            return false;
        }

        $matches = $this->substitutePlaceholderNames($matches, $rule);

 

        foreach ($rule->defaults as $name => $value) {
            if (!isset($matches[$name]) || $matches[$name] === '') {
                $matches[$name] = $value;
            }
        }
        $params = $rule->defaults;
        $tr = [];
        foreach ($matches as $name => $value) {
            
            if (isset($rule->_routeParams[$name])) {
                $tr[$rule->_routeParams[$name]] = $this->callback($value, $rule->_routeParams[$name], $rule);
                unset($params[$name]);
            } elseif (isset($rule->_paramRules[$name])) {
                //(($rule->encodeParams) ? urlencode((string) $value) : $value)
                $params[$name] = $value;
            }
        }



        if ($rule->_routeRule !== null) {

            $route = strtr($rule->route, $tr);
        } else {
            $route = $rule->route;
        }

        //_var_dump("Request parsed with URL rule: {$this->name}", __METHOD__);

        return [
            'rule' => $rule->name,
            'route' => $route,
            'params' => $params
        ];
    }

    protected function callback($value, $name, $rule)
    {


        if (empty($rule->callback) || !array_key_exists($name, $rule->callback)) {
            return $value;
        }

        foreach ((array) $rule->callback[$name] as $function) {
            $value = $function($value);
        }

        return $value;
    }

    protected function substitutePlaceholderNames(array $matches, \Enjoys\Route\Rule $rule)
    {
        foreach ($rule->placeholders as $placeholder => $name) {
            if (isset($matches[$placeholder])) {
                $matches[$name] = $matches[$placeholder];
                unset($matches[$placeholder]);
            }
        }
        return $matches;
    }
}
