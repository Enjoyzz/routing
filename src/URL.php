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

       
        $createUrl = new \Enjoys\Route\Url\Create($route, $params, static::getUrlManager());
        
        $url = $createUrl->returnUrl();
        
        if ($scheme === true) {
            $url = $createUrl->createAbsoluteUrl($url);
        }
        return $url;
    }




    public static function make($url = '', $scheme = false){
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
