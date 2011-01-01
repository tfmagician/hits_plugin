<?php
/**
 * Hit model
 *
 * @package hits
 * @subpackage hits.models
 */
class Hit extends HitsAppModel
{

    /**
     * primary key
     *
     * @access public
     * @var string
     */
    var $primaryKey = 'url';

    /**
     * Increase count for a page.
     *
     * @param string $url  URL string for the page counted.
     * @return array  same values as Model::save() method.
     */
    function count($url)
    {
        $params = array(
            'conditions' => array($this->alias.'.url = ' => $url),
            'fields' => array(
                $this->alias.'.url',
                $this->alias.'.hits',
            ),
            'recursive' => -1,
        );
        if (!$data = $this->find('first', $params)) {
            $data = array(
                $this->alias => array(
                    'url' => $url,
                    'hits' => 0,
                ),
            );
        }
        $data[$this->alias]['hits'] ++;
        $this->create();
        return $this->save($data);
    }

}
