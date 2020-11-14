<?php

namespace Enjoys\Route;

/**
 * @https://github.com/yiisoft/yii2/blob/master/framework/helpers/BaseUrl.php
 */
class URL
{

    public static Manager $urlManager;



    public static function setUrlmanager(Manager $urlManager): void
    {
        static::$urlManager = $urlManager;
    }

    public static function getUrlManager(): Manager
    {
        return static::$urlManager;
    }

    /**
     *
     * @param  string      $route
     * @param  array       $params
     * @param  bool|string $scheme
     * @return string
     */
    public static function toRoute(string $route, array $params, $scheme = false): string
    {


        $createUrl = new \Enjoys\Route\Url\Create($route, $params, static::getUrlManager());

        $url = $createUrl->returnUrl();

        if ($scheme !== false) {
            $url = $createUrl->createAbsoluteUrl($url, $scheme);
        }
        return $url;
    }




    /**
     *
     * @param  string|array $url
     * @param  bool|string  $scheme
     * @return string
     */
    public static function make($url = '', $scheme = false)
    {
        if (is_array($url)) {
            $params = $url;
            $route = array_shift($params);

            return static::toRoute($route, $params, $scheme);
        }
        //       // $url = Yii::getAlias($url);
        //        if ($url === '') {
        //            $url = $this->requ->getUrl();
        //        }
        if ($scheme === false) {
            return $url;
        }
        //        if (static::isRelative($url)) {
        //            // turn relative URL into absolute
        //            $url = static::$urlManager->getHostInfo() . '/' . ltrim($url, '/');
        //        }
        return Helpers::ensureScheme($url, $scheme);
    }
}
