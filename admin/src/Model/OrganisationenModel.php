<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Model für die Organisationen-Übersicht im Backend.
 * Holt die Daten aus der Datenbank, verarbeitet Filter, Sortierung und Pagination.
 * Bindeglied zwischen Datenbank und View.
 * Für weitere Views kann dieses Model als Vorlage dienen.
 */
class OrganisationenModel extends ListModel
{
	public function __construct($config = [])
	{
		// Definiert, welche Felder für Filter und Sortierung verwendet werden können
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id',
				'a.id',
				'title',
				'a.title',
				'ordering',
				'a.ordering',
			];
		}
		parent::__construct($config);
	}

	/**
	 * Setzt den State für Filter, Sortierung und Pagination.
	 * Wird beim Laden der Liste aufgerufen.
	 */
	protected function populateState($ordering = 'a.ordering', $direction = 'ASC')
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

	/**
	 * Baut die SQL-Abfrage für die Einsatzkategorien-Liste.
	 * Berücksichtigt Filter, Sortierung und Joins zu anderen Tabellen.
	 */
	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.title'),
					$db->quoteName('a.ordering'),
					$db->quoteName('a.created'),
					$db->quoteName('a.modified'),
					$db->quoteName('a.created_by'),
					$db->quoteName('a.modified_by'),
					$db->quoteName('uc.name', 'created_by_name'),
					$db->quoteName('um.name', 'modified_by_name'),
				]
			)
		)->from($db->quoteName('#__blaulichtmonitor_organisationen', 'a'))
			// Join für created_by
			->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON ' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.created_by'))
			// Join für modified_by
			->join('LEFT', $db->quoteName('#__users', 'um') . ' ON ' . $db->quoteName('um.id') . ' = ' . $db->quoteName('a.modified_by'));

		// Filter: Suchbegriff
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->quote('%' . str_replace(
				' ',
				'%',
				$db->escape(trim($search), true) . '%'
			));

			$where = [
				'a.id LIKE ' . $search,
				'a.title LIKE ' . $search,
				'uc.name LIKE ' . $search, // created_by_name
				'um.name LIKE ' . $search, // modified_by_name
			];

			$query->where('(' . implode(' OR ', $where) . ')');
		}

		// Default-Sortierung nach Alarmierungszeit
		$orderCol  = $this->state->get('list.ordering', 'a.ordering');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Lädt das Filterformular (siehe filter_alarmierungsarten.xml).
	 * Wird im View verwendet, um das Such- und Sortierformular anzuzeigen.
	 */
	public function getFilterForm($data = [], $loadData = true)
	{
		return $this->loadForm(
			'com_blaulichtmonitor.organisationen',
			'filter_organisationen',
			['control' => '', 'load_data' => $loadData]
		);
	}
}
