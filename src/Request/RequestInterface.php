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

    /**
     * @return string The raw path (i.e. not urldecoded)
     */
    public function getPathInfo();

    /**
     * @return string The request method
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getHost();

    /**
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key = null, $default = null);
}
