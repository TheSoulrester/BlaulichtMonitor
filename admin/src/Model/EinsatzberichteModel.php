<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

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
		$app   = Factory::getApplication();
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
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.veroeffentlicht'),
					$db->quoteName('a.alarmierungszeit'),
					$db->quoteName('a.einsatzart_id'),
					$db->quoteName('a.einsatzort_strasse'),
					$db->quoteName('a.einsatzkurzbericht'),
					$db->quoteName('a.counter_clicks'),
					$db->quoteName('a.created'),
					$db->quoteName('a.modified'),
					// Einsatzart-Title
					$db->quoteName('b.title', 'einsatzart_title'),
					// Einheiten als Liste
					'(SELECT GROUP_CONCAT(e.title SEPARATOR ", ")
                        FROM #__blaulichtmonitor_einsatzberichte_einheiten ee
                        INNER JOIN #__blaulichtmonitor_einheiten e ON ee.einheit_id = e.id
                        WHERE ee.einsatzbericht_id = a.id
                    ) AS einheiten_liste',
				]
			)
		)->from($db->quoteName('#__blaulichtmonitor_einsatzberichte', 'a'))
			->join('LEFT', $db->quoteName('#__blaulichtmonitor_einsatzarten', 'b') . ' ON ' . $db->quoteName('a.einsatzart_id') . ' = ' . $db->quoteName('b.id'));

		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = $db->quote('%' . str_replace(
				' ',
				'%',
				$db->escape(trim($search), true) . '%'
			));
			$query->where('(a.alarmierungszeit LIKE ' . $search .
				' OR a.einsatzort_strasse LIKE ' . $search .
				' OR a.einsatzkurzbericht LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.alarmierungszeit');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	public function getFilterForm($data = [], $loadData = true)
	{
		return $this->loadForm(
			'com_blaulichtmonitor.einsatzberichte',
			'filter_einsatzberichte',
			['control' => '', 'load_data' => $loadData]
		);
	}
}
