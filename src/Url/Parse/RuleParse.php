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

    public function parse(): array
    {
        foreach ($this->getManager()->getRules() as $rule) {

            $result = $this->parseRequest($rule);

            if ($result !== false) {
                return $result;
            }
        }

        //        if ($this->getManager()->getOption('enableStrictParsing')) {
        //            return false;
        //        }

        return parent::parse();
    }

    /**
     * 
     * @param  \Enjoys\Route\Rule $rule
     * @return false|array{rule: string|null, route: string|null, params: array<array-key, mixed>}
     */
    private function parseRequest(\Enjoys\Route\Rule $rule)
    {
        if ($rule->mode === \Enjoys\Route\Rule::CREATION_ONLY) {
            return false;
        }

        if (!empty($rule->verb) && !in_array($this->getRequest()->getMethod(), $rule->verb, true)) {
            return false;
        }

        $suffix = (string) (($rule->suffix === null) ? $this->getManager()->getSuffix() : $rule->suffix);

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
            
            if (isset($rule->routeParams[$name])) {
                $tr[$rule->routeParams[$name]] = $this->callback($value, $rule->routeParams[$name], $rule);
                unset($params[$name]);
            } elseif (isset($rule->ruleParams[$name])) {
                //(($rule->encodeParams) ? urlencode((string) $value) : $value)
                $params[$name] = $value;
            }
        }



        if ($rule->ruleRoute !== null) {

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

    protected function callback(string $value, string $name, \Enjoys\Route\Rule $rule): string
    {


        if (!array_key_exists($name, $rule->callback)) {
            return $value;
        }

        foreach ((array) $rule->callback[$name] as $function) {
            $value = $function($value);
        }

        return $value;
    }

    protected function substitutePlaceholderNames(array $matches, \Enjoys\Route\Rule $rule): array
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
