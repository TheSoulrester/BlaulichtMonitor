<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class EinsatzberichteModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'a.id',
                'alarmierungszeit',
                'a.alarmierungszeit',
            ];
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = 'alarmierungszeit', $direction = 'DESC')
    {
        $app = Factory::getApplication();
        $value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
        $this->setState('list.limit', $value);
        $value = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $value);
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
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
                    $db->quoteName('a.alarmierungszeit'),
                    $db->quoteName('a.beschreibung'),
                ]
            )
        )->from($db->quoteName('#__blaulichtmonitor_einsatzberichte', 'a'));
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . str_replace(
                ' ',
                '%',
                $db->escape(trim($search), true) . '%'
            ));
            $query->where('(a.alarmierungszeit LIKE ' . $search . ')');
        }
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
