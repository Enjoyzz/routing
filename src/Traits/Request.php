<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Enjoys\Route\Traits;

/**
 *
 * @author Enjoys
 */
trait Request
{

    protected \Enjoys\Route\Request\RequestInterface $request;

    protected function getRequest(): \Enjoys\Route\Request\RequestInterface
    {
        return $this->request;
    }
}
