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

    public static function isRelative(string $url): bool
    {
        return strncmp($url, '//', 2) && strpos($url, '://') === false;
    }

    /**
     * 
     * @param  string      $url
     * @param  string|bool $scheme
     * @return string
     */
    public static function ensureScheme(string $url, $scheme)
    {
        if (self::isRelative($url) || !is_string($scheme)) {
            return $url;
        }
        if (substr($url, 0, 2) === '//') {
            // e.g. //example.com/path/to/resource
            return $scheme === '' ? $url : "$scheme:$url";
        }
        if (($pos = strpos($url, '://')) !== false) {
            if ($scheme === '') {
                $url = substr($url, $pos + 1);
            } else {
                $url = $scheme . substr($url, $pos);
            }
        }
        return $url;
    }
}
