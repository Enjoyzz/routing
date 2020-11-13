<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Request;

/**
 * Class RequestInterface
 *
 * @author Enjoys
 */
interface RequestInterface
{
    public function getPathInfo();
    public function getMethod();
    public function getHost();
    /**
     * 
     * @param string|null $key
     * @param mixed $default
     */
    public function get(string $key = null, $default = null);
}
