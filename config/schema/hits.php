<?php
/**
 * Schema definition for HitsPlugin.
 *
 * @package hits
 * @subpackage hits.config.schema
 */
class HitsSchema extends CakeSchema
{

    /**
     * Schema name
     *
     * @access public
     * @var string
     */
    var $name = 'Hits';

    /**
     * hits table to contain number of hits.
     *
     * @access public
     * @var array
     */
    var $hits = array(
        'url'       => array('type' => 'string',   'null' => false, 'default' => NULL, 'key' => 'primary'),
        'hits'      => array('type' => 'integer',  'null' => false, 'default' => NULL),
        'created'   => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'modified'  => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array(
            'PRIMARY' => array('column' => 'url',  'unique' => 1),
            'hits'    => array('column' => 'hits', 'unique' => 0),
        ),
        'tableParameters' => array(
            'charset' => 'utf8',
            'collate' => 'utf8_unicode_ci',
            'engine'  => 'InnoDB',
        ),
    );

}
