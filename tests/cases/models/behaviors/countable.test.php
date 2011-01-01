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
        $params = array(
            'belongsTo' => array(
                'Hit' => array(
                    'className' => 'Hits.Hit',
                    'foreignKey' => false,
                    'conditions' => array(
                        '`Hit`.`url` = CONCAT("/this/is/url/", `Post`.`id`)',
                    ),
                    'fields' => null,
                    'order' => null,
                    'dependent' => true,
                ),
            ),
        );
        $this->Post->expectOnce('bindModel', array($params, false));

        $config = array(
            'url' => '/this/is/url/:id',
        );
        $this->CountableBehavior->setup($this->Post, $config);
    }

}
