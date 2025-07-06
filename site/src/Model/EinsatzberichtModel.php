<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class EinsatzberichtModel extends BaseDatabaseModel
{
    protected $_item = null;

    protected function populateState()
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $id = $app->input->getInt('id');
        $this->setState('einsatzbericht.id', $id);
        $this->setState('einsatzbericht.params', $params);
    }

    function getItem($pk = null)
    {
        $id = (int) $pk ?: (int) $this->getState('einsatzbericht.id');
        if (!$id) {
            throw new \Exception('Missing einsatzbericht id', 404);
        }
        if ($this->_item !== null && $this->_item->id != $id) {
            return $this->_item;
        }
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__blaulichtmonitor_einsatzberichte', 'a'))
            ->where($db->quoteName('a.id') . ' = ' . (int)
            $id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (!empty($item)) {
            $this->_item = $item;
        }
        return $this->_item;
    }
}
