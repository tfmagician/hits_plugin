<?php
App::import('Component', 'Hits.Counter');
App::import('Component', 'RequestHandler');
App::import('Model', 'Hits.Hit');

Mock::generate('Hit', 'CounterComponent_Hit');
Mock::generate('RequestHandlerComponent', 'CounterComponent_RequestHandler');
Mock::generatePartial(
    'Controller', 'TestController',
    array('redirect', 'cakeError')
);

class CounterComponentTestCase extends CakeTestCase
{

    var $fixtures = array(
        'plugin.hits.hit',
    );

    function startTest()
    {
        $this->Controller = new TestController();
        $this->CounterComponent = new CounterComponent();
        $this->CounterComponent->initialize($this->Controller);
        $this->CounterComponent->Hit = new CounterComponent_Hit();
        $this->CounterComponent->RequestHandler = new CounterComponent_RequestHandler();

        Configure::write('Cache.disable', false);
        Cache::clear();
    }

    function endTest()
    {
        unset($this->Controller);
        unset($this->CounterComponent);
        Configure::write('debug', 2);
    }

    function testStartup()
    {
        $Hit = $this->CounterComponent->Hit;
        $Hit->expectOnce('count', array('/this/is/url'));
        $this->Controller->params = array(
            'url' => array(
                'url' => 'this/is/url',
            ),
        );
        $this->CounterComponent->startup($this->Controller);
    }

    function testStartupIgnoredByIPAddress()
    {
        $Hit = $this->CounterComponent->Hit;
        $Hit->expectOnce('count', array('/this/is/url'));

        $RequestHandler = $this->CounterComponent->RequestHandler;
        $RequestHandler->expectCallCount('getClientIP', 3);
        $RequestHandler->expectAt(0, 'getClientIP', array());
        $RequestHandler->setReturnValueAt(0, 'getClientIP', '100.100.100.102');
        $RequestHandler->expectAt(1, 'getClientIP', array());
        $RequestHandler->setReturnValueAt(1, 'getClientIP', '100.100.100.100');
        $RequestHandler->expectAt(2, 'getClientIP', array());
        $RequestHandler->setReturnValueAt(2, 'getClientIP', '100.100.100.101');

        $this->Controller->params = array(
            'url' => array(
                'url' => 'this/is/url',
            ),
        );
        $this->CounterComponent->ignore = array(
            'ipAddresses' => array(
                '100.100.100.100',
                '100.100.100.101',
            ),
        );
        for ($i = 0; $i < 3; $i ++) {
            $this->CounterComponent->startup($this->Controller);
        }
    }

    function testStartupIgnoredUserAgent()
    {
        $Hit = $this->CounterComponent->Hit;
        $Hit->expectOnce('count', array('/this/is/url'));

        $this->Controller->params = array(
            'url' => array(
                'url' => 'this/is/url',
            ),
        );
        $this->CounterComponent->ignore = array(
            'userAgents' => array(
                'ignore1',
                'IGNORE2',
            ),
        );
        $agents = array(
            'ignore1',
            'ignore2',
            'agent',
        );
        foreach ($agents as $agent) {
            $_SERVER['HTTP_UESR_AGENT'] = $agent;
            $this->CounterComponent->startup($this->Controller);
        }
    }

    function testStartupIgnoredBySecondAccess()
    {
        Configure::write('debug', 0);

        $Hit = $this->CounterComponent->Hit;
        $Hit->expectCallCount('count', 2);

        $RequestHandler = $this->CounterComponent->RequestHandler;
        $RequestHandler->expectCallCount('getClientIP', 3);
        $RequestHandler->expectAt(0, 'getClientIP', array());
        $RequestHandler->setReturnValueAt(0, 'getClientIP', '100.100.100.101');
        $RequestHandler->expectAt(1, 'getClientIP', array());
        $RequestHandler->setReturnValueAt(1, 'getClientIP', '100.100.100.100');
        $RequestHandler->expectAt(2, 'getClientIP', array());
        $RequestHandler->setReturnValueAt(2, 'getClientIP', '100.100.100.101');

        $this->Controller->params = array(
            'url' => array(
                'url' => 'this/is/url',
            ),
        );
        $this->CounterComponent->ignore = array(
            'ipAddresses' => array(),
            'userAgents' => array(),
        );
        for ($i = 0; $i < 3; $i ++) {
            $this->CounterComponent->startup($this->Controller);
        }
    }

}
