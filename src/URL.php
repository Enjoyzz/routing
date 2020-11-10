<?php
namespace Enjoys\Route;



/**
 * @https://github.com/yiisoft/yii2/blob/master/framework/helpers/BaseUrl.php
 */
class URL
{
    
    public static Manager $urlManager;
   
    
  
    public static function setUrlmanager(Manager $urlManager) {
        static::$urlManager = $urlManager;
    }
    
    public static function getUrlManager() : Manager {
        return static::$urlManager;
    }
    
    public static function toRoute(string $route, array $params, $scheme = false)
    {

       // $url = static::getUrlManager()->createUrl($route, $params);
        
        $createUrl = new CreateUrl($route, $params, static::getUrlManager());
        $url = $createUrl->returnUrl();
        
        if ($scheme === true) {
            $url = $createUrl->createAbsoluteUrl($url);
        }
        return $url;
    }


//    
//    public static function canonical($route, $params)
//    {
//        return static::getUrlManager()->createAbsoluteUrl(array_merge((array)$route, $params));
//    }    
//            

    public static function make($url = '', $scheme = false){
        if (is_array($url)) {
            $params = $url;
            $route = array_shift($params);

            return static::toRoute($route, $params, $scheme);
        }
//       // $url = Yii::getAlias($url);
//        if ($url === '') {
//            $url = Request::getInstance()->getUrl();
//        }
//        if ($scheme === false) {
//            return $url;
//        }
//        if (static::isRelative($url)) {
//            // turn relative URL into absolute
//            $url = static::$urlManager->getHostInfo() . '/' . ltrim($url, '/');
//        }
//        return static::ensureScheme($url, $scheme);
    }
    
  
//    public static function ensureScheme($url, $scheme)
//    {
//        if (static::isRelative($url) || !is_string($scheme)) {
//            return $url;
//        }
//        if (substr($url, 0, 2) === '//') {
//            // e.g. //example.com/path/to/resource
//            return $scheme === '' ? $url : "$scheme:$url";
//        }
//        if (($pos = strpos($url, '://')) !== false) {
//            if ($scheme === '') {
//                $url = substr($url, $pos + 1);
//            } else {
//                $url = $scheme . substr($url, $pos);
//            }
//        }
//        return $url;
//    }
//
//    public static function base($scheme = false)
//    {
//        $url = static::$urlManager->getBaseUrl();
//        if ($scheme !== false) {
//            $url = static::$urlManager->getHostInfo() . $url;
//            $url = static::ensureScheme($url, $scheme);
//        }
//        return $url;
//    }

   
//    public static function home($scheme = false)
//    {
//        $url = Yii::$app->getHomeUrl();
//        if ($scheme !== false) {
//            $url = static::$urlManager->getHostInfo() . $url;
//            $url = static::ensureScheme($url, $scheme);
//        }
//        return $url;
//    }
    /**
     * Returns a value indicating whether a URL is relative.
     * A relative URL does not have host info part.
     * @param string $url the URL to be checked
     * @return bool whether the URL is relative
     */
//    public static function isRelative($url)
//    {
//        return strncmp($url, '//', 2) && strpos($url, '://') === false;
//    }

//    public static function current(array $params = [], $scheme = false)
//    {
//        $currentParams = Request::getInstance()->getQueryParams();
//        $currentParams[0] = '/' . Request::getInstance()->getRoute();
//        $route = array_replace_recursive($currentParams, $params);
//        return static::toRoute($route, $scheme);
//    }
    
  

}
