<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

class EinsatzberichtModel extends AdminModel
{
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_blaulichtmonitor.einsatzbericht',
			'einsatzbericht',
			[
				'control'   => 'jform',
				'load_data' => $loadData,
			]
		);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState(
			'com_blaulichtmonitor.edit.einsatzbericht.data',
			[]
		);
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		$db = Factory::getContainer()->get('DatabaseDriver');

		// Einsheiten laden (Join-Tabelle)
		$query = $db->getQuery(true)
			->select('einheit_id')
			->from($db->quoteName('#__blaulichtmonitor_einsatzberichte_einheiten'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);
		$item->einheiten_id = $db->loadColumn();

		// Einsatzfahrzeuge laden (Join-Tabelle)
		$query = $db->getQuery(true)
			->select('fahrzeug_id')
			->from($db->quoteName('#__blaulichtmonitor_einsatzberichte_fahrzeuge'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);
		$item->fahrzeuge_id = $db->loadColumn();

		// Presseberichte laden
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__blaulichtmonitor_einsatzberichte_presse'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);
		$item->presseberichte = $db->loadAssocList();

		return $item;
	}
}
