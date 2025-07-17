<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;

class EinsatzberichtTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__blaulichtmonitor_einsatzberichte', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		// Leere Datumsfelder auf null setzen
		foreach (['alarmierungszeit', 'ausrueckzeit', 'einsatzende'] as $field) {
			if (isset($array[$field]) && $array[$field] === '') {
				$array[$field] = null;
			}
		}
		// Leere Zahlenfelder auf null setzen
		foreach (['people_count'] as $field) {
			if (isset($array[$field]) && $array[$field] === '') {
				$array[$field] = null;
			}
		}

		/*
		// Leere Priorität auf null setzen, wenn 0 ausgewählt wurde
		foreach (['prioritaet'] as $field) {
			if (isset($array[$field]) && ($array[$field] === '0' || $array[$field] === 0)) {
				$array[$field] = null;
			}
		}
		*/

		// published auf 0 setzen, wenn nicht gesetzt (Checkbox nicht angehakt)
		if (!isset($array['published'])) {
			$array['published'] = 0;
		}

		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		$user = Factory::getApplication()->getIdentity();
		$now  = Factory::getDate()->toSql();

		if ($this->id) {
			// Update
			$this->modified    = $now;
			$this->modified_by = (int) $user->id;
		} else {
			// Neu
			$this->created    = $now;
			$this->created_by = (int) $user->id;
		}

		// Falls modified_by/created_by leer sind, auf NULL setzen
		if (empty($this->created_by)) {
			$this->created_by = null;
		}
		if (empty($this->modified_by)) {
			$this->modified_by = null;
		}

		return parent::store($updateNulls);
	}
}
