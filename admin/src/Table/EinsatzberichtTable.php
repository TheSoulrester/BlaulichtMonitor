<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

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
		// Leere Priorität auf null setzen
		foreach (['prioritaet'] as $field) {
			if (isset($array[$field]) && $array[$field] === '') {
				$array[$field] = null;
			}
		}
		return parent::bind($array, $ignore);
	}
}
