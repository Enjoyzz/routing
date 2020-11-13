<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Request;

/**
 * @psalm-suppress PropertyNotSetInConstructor 
 * @author Enjoys
 */
class Request extends \Symfony\Component\HttpFoundation\Request implements RequestInterface
{
    /**
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function get(?string $key = null, $default = null)
    {
        if($key === null){
            return $this->query->all();
        }
        return parent::get($key, $default);
    }
    
    
    public function addQuery(array $params): void
    {
        $this->query->add($params);
    }
}
