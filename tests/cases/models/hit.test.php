<?php
App::import('Model', 'Hits.Hit');

class HitTestCase extends CakeTestCase
{

    var $fixtures = array(
        'plugin.hits.hit',
    );

    function startTest()
    {
        $this->Hit = ClassRegistry::init('Hit');
    }

    function endTest()
    {
        unset($this->Hit);
        ClassRegistry::flush();
    }

    function testCount()
    {
        $url = '/this/is/url';
        for ($i = 0; $i < 3; $i ++) {
            $ret = $this->Hit->count($url);
            $this->assertTrue($ret);
            $params = array(
                'fields' => array('url', 'hits'),
                'recursive' => -1,
            );
            $ret = $this->Hit->find('first', $params);
            $expected = array(
                'Hit' => array(
                    'url' => $url,
                    'hits' => $i + 1,
                ),
            );
            $this->assertEqual($expected, $ret);
       }
    }

}
