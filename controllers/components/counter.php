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
     * Initialization
     *
     * @access public
     * @param object $Controller  Controller with components to initialize
     * @param array $config
     * @return void
     */
    function initialize(&$Controller, $config = array())
    {
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
        if ($agent = env('HTTP_UESR_AGENT')) {
            foreach ($this->ignore['userAgents'] as $ignore) {
                if (preg_match('/'.$ignore.'/iu', $agent)) {
                    return;
                }
            }
        }
        $this->Hit->count('/'.$Controller->params['url']['url']);
    }

}
