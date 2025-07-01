<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

class EinsatzberichteModel extends ListModel
{
    protected function populateState($ordering = 'alarmierungszeit', $direction = 'DESC')
    {
        $app = Factory::getApplication();
        $value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
        $this->setState('list.limit', $value);
        $value = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $value);
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.beschreibung'),
                    $db->quoteName('a.alarmierungszeit'),
                ]
            )
        )->from($db->quoteName('#__blaulichtmonitor_einsaetze', 'a'));
        $orderCol = $this->state->get(
            'list.ordering',
            'a.alarmierungszeit'
        );
        $orderDirn = $this->state->get(
            'list.direction',
            'DESC'
        );
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        return $query;
    }
}
