<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Url\Create;

/**
 * Class Rule
 *
 * @author Enjoys
 */
class Rule extends Base implements \Enjoys\Route\Url\CreateInterface
{

    public function returnUrl(): string
    {
        foreach ($this->getManager()->getRules() as $rule) {
            $url = $this->create($rule);
            if ($url !== false) {
                break;
            }
        }

        if ($url === false) {
            return parent::returnUrl();
        }

        return "{$url}{$this->anchor}";
    }

    /**
     * 
     * @param \Enjoys\Route\Rule $rule
     * @return false|string
     */
    private function create(\Enjoys\Route\Rule $rule)
    {
        $translate = [];

        if ($this->route !== $rule->route) {
            return false;
        }



        // match default params
        foreach ($rule->defaults as $name => $value) {
            $translate["<$name>"] = $value;
        }

        // match params in the pattern

        foreach ($rule->_paramRules as $name => $_rule) {
          
            if (
                    array_key_exists($name, $this->params) &&
                    !is_array($this->params[$name]) &&
                    ($_rule === '' || preg_match($_rule, (string) $this->params[$name]))
            ) {
                $translate["<$name>"] = ($rule->encodeParams) ? urlencode((string) $this->params[$name]) : $this->params[$name];

                unset($this->params[$name]);
                
            } elseif (
                    !isset($rule->defaults[$name]) ||
                    isset($this->params[$name])
            ) {
              
                return false;
            }
        }



        $url = $this->buildUrl(\Enjoys\Route\Helpers::trimSlashes(strtr($rule->_template, $translate)), $rule);

        if ($rule->host === null) {
            return $this->baseUrl . $url;
        }

        return $url;
    }

    private function buildUrl($url, \Enjoys\Route\Rule $rule): string
    {

        if ($rule->host !== null) {
            $pos = strpos($url, '//', 0) + 1;
            if ($pos !== false) {
                $url = substr($url, 0, $pos) . preg_replace('#/+#', '/', substr($url, $pos));
            }
        } elseif (strpos($url, '//') !== false) {
            $url = preg_replace('#/+#', '/', trim($url, '/'));
        }
        if ($url !== '') {

            $url .= ($rule->suffix === null ? $manager->suffix : $rule->suffix);
        }
        if (!empty($this->params) && ($query = http_build_query($this->params)) !== '') {

            $url .= '?' . $query;
        }
        return $url;
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
