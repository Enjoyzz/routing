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
        $suffix = (string) $this->getManager()->suffix;
        $pathInfo = $this->request->getPathInfo();

        if ($suffix !== '' && $pathInfo !== '') {
            $n = strlen($this->getManager()->suffix);
            if (substr_compare($pathInfo, $this->getManager()->suffix, -$n, $n) === 0) {
                $pathInfo = substr($pathInfo, 0, -$n);
                if ($pathInfo === '') {
                    // suffix alone is not allowed
                    return false;
                }
            } else {
                // suffix doesn't match
                return false;
            }
        }
        return ['route' => $pathInfo, 'params' => []];
    }
}
