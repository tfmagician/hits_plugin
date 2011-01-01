<?php
class HitFixture extends CakeTestFixture
{
    var $name = 'Hit';

    var $fields = array(
        'url'       => array('type' => 'string',   'null' => false, 'default' => NULL, 'key' => 'primary'),
        'hits'      => array('type' => 'integer',  'null' => false, 'default' => NULL),
        'created'   => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'modified'  => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array(
            'PRIMARY' => array('column' => 'url',  'unique' => 0),
            'hits'    => array('column' => 'hits', 'unique' => 0),
        ),
        'tableParameters' => array(
            'charset' => 'utf8',
            'collate' => 'utf8_unicode_ci',
            'engine'  => 'InnoDB',
        ),
    );

}
