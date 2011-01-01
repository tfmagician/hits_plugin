<?php
/**
 * Countable Behavior
 *
 * @package hits
 * @subpackage hits.models.behaviors
 */
class CountableBehavior extends ModelBehavior
{

    /**
     * setup
     *
     * @access public
     * @param object $Model
     * @param array $config
     */
    function setup(&$Model, $config = array())
    {
        if (!isset($config['url'])) {
            trigger_error('Could not found url key in config array.', E_USER_WARNING);
            return;
        }
        extract($config);

        $fields = array_keys($Model->schema());
        $regex = '/(:(=?'.implode('|', $fields).'))/u';
        preg_match_all($regex, $url, $matches, PREG_PATTERN_ORDER);
        $matches = $matches[1];

        $args = array();
        foreach ($matches as $match) {
            list($separated, $url) = split($match, $url);
            $args[] = sprintf('"%s"', $separated);
            $args[] = sprintf('`%s`.`%s`', $Model->alias, str_replace(':', '', $match));
        }
        $Model->bindModel(
            array(
                'belongsTo' => array(
                    'Hit' => array(
                        'className' => 'Hits.Hit',
                        'foreignKey' => false,
                        'conditions' => array(
                            '`Hit`.`url` = '.'CONCAT('.implode(', ', $args).')',
                        ),
                        'fields' => null,
                        'order' => null,
                        'dependent' => true,
                    ),
                ),
            ),
            false
        );
    }

}
