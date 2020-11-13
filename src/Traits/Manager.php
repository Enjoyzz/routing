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
trait Manager
{
    protected \Enjoys\Route\Manager $manager;
    
    protected function getManager(): \Enjoys\Route\Manager
    {
        return $this->manager;
    }    
}
