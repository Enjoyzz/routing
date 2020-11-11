<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route;

/**
 * Class Helpers
 *
 * @author Enjoys
 */
class Helpers
{

    public static function trimSlashes(string $string): string
    {
        if (strncmp($string, '//', 2) === 0) {
            return '//' . trim($string, '/');
        }
        return trim($string, '/');
    }
}
