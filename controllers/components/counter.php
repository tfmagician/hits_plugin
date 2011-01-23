<?php
/**
 * Counter component.
 *
 * @package hits
 * @subpackage hits.controllers.components
 */
class CounterComponent extends Object
{

    /**
     * Environments ignored.
     *
     * @access public
     * @var array  Could have 'ipAddresses' and 'userAgents' keys.
     */
    var $ignore = array(
        'ipAddresses' => array(),
        'userAgents' => array(),
    );

    /**
     * Components to use.
     *
     * @access public
     * @var array
     */
    var $components = array(
        'RequestHandler',
    );

    /**
     * Cache config for CounterComponent::isSecondAccess().
     *
     * @access public
     * @var array
     */
    var $cacheConfig = array(
        'engine' => 'File',
        'duration'=> 3600,
        'path' => CACHE,
        'prefix' => 'hits_',
        'lock' => false,
        'serialize' => true,
    );

    /**
     * Checks second access by IP address.
     *
     * @access public
     * @param string $ip
     * @param string $url
     * @return boolean  true if this access is second.
     */
    function isSecondAccess($ip, $url)
    {
        if (!$cacheURLs = Cache::read($ip, 'hits')) {
            $cacheURLs = array();
        }
        if (in_array($url, $cacheURLs)) {
            return true;
        }
        $cacheURLs[] = $url;
        Cache::write($ip, $cacheURLs, 'hits');
        return false;
    }

    /**
     * Initialization
     *
     * @access public
     * @param object $Controller  Controller with components to initialize
     * @param array $config
     * @return void
     */
    function initialize(&$Controller, $config = array())
    {
        Cache::config('hits', $this->cacheConfig);
        $this->Hit =& ClassRegistry::init('Hits.Hit');
        if (isset($config['ignore'])) {
            $this->ignore = $config['ignore'] + $this->ignore;
        }
    }

    /**
     * Startup
     *
     * @access public
     * @param object  $controller Controller with components to statup
     * @return void
     */
    function startup(&$Controller)
    {
        if ($ip = $this->RequestHandler->getClientIP()) {
            if (in_array($ip, $this->ignore['ipAddresses'])) {
                return;
            }
        }
        if ($agent = env('HTTP_USER_AGENT')) {
            foreach ($this->ignore['userAgents'] as $ignore) {
                if (preg_match('/'.$ignore.'/iu', $agent)) {
                    return;
                }
            }
        }
        $url = '/'.$Controller->params['url']['url'];
        if (!$this->isSecondAccess($ip, $url)) {
            $this->Hit->count($url);
        }
    }

}
