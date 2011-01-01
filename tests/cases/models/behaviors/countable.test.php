<?php
App::import('Behavior', 'Hits.Countable');

class CountableBehavior_Post extends Model
{
    var $useTable = false;
    var $alias = 'Post';
}

Mock::generate('CountableBehavior_Post');

class CountableBehaviorTestCase extends CakeTestCase
{

    function startTest()
    {
        $this->Post = new MockCountableBehavior_Post();
        $this->CountableBehavior = new CountableBehavior();
    }

    function endTest()
    {
        unset($this->Post);
        unset($this->CountableBehavior);
    }

    function testSetup()
    {
        $this->Post->expectOnce('schema', array());
        $schema = array(
            'id' => array(),
            'title' => array(),
            'content' => array(),
        );
        $this->Post->setReturnValue('schema', $schema);

        $config = array(
            'url' => '/this/is/url/:id',
        );
        $this->CountableBehavior->setup($this->Post, $config);
        $expected = array(
            'Hits.Hit' => array(
                'className' => 'Hits.Hit',
                'foreignKey' => 'url',
                'conditions' => array(),
                'fields' => null,
                'order' => null,
                'dependent' => false,
            ),
        );
        $this->assertEqual($this->Post->belongsTo, $expected);
        $expected = array(
            'url' => 'CONCAT("/this/is/url/", `Post`.`id`)',
        );
        $this->assertEqual($expected, $this->Post->virtualFields);
    }

}
