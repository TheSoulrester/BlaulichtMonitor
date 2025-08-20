<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;

/**
 * Table-Klasse für Einheiten.
 * Diese Klasse kapselt die Datenbankoperationen für die Einheiten-Tabelle
 * und sorgt für die korrekte Vorverarbeitung und Speicherung der Daten.
 */
class EinheitTable extends Table
{
	/**
	 * Konstruktor: Initialisiert die Tabelle mit Name und Primärschlüssel.
	 *
	 * @param DatabaseDriver $db Datenbanktreiber-Objekt
	 */
	public function __construct(DatabaseDriver $db)
	{
		// Initialisiere die Tabelle mit dem Namen und dem Primärschlüssel-Feld
		parent::__construct('#__blaulichtmonitor_einheiten', 'id', $db);
	}

	/**
	 * Bindet die übergebenen Daten an das Table-Objekt.
	 * Setzt leere Felder auf null und sorgt für die korrekte Vorbelegung.
	 *
	 * @param array  $array  Die zu bindenden Daten
	 * @param string $ignore Felder, die ignoriert werden sollen
	 * @return bool
	 */
	public function bind($array, $ignore = '')
	{
		// Keine speziellen Felder für Einsatzarten nötig, aber Platz für spätere Anpassungen
		return parent::bind($array, $ignore);
	}

	/**
	 * Speichert das Table-Objekt in der Datenbank.
	 * Setzt automatisch die Felder für Erstellungs-/Änderungsdatum und Benutzer.
	 *
	 * @param bool $updateNulls Sollen NULL-Werte aktualisiert werden?
	 * @return bool Erfolg/Misserfolg des Speicherns
	 */
	public function store($updateNulls = false)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$now  = \Joomla\CMS\Factory::getDate()->toSql();

		// Automatische Sortierung für neue Datensätze
		if (!$this->id) {
			// Nur wenn ordering nicht gesetzt ist
			if (empty($this->ordering)) {
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select('MAX(' . $db->quoteName('ordering') . ')')
					->from($db->quoteName($this->_tbl));
				$db->setQuery($query);
				$max = (int) $db->loadResult();
				$this->ordering = $max + 1;
			}
			$this->created    = $now;
			$this->created_by = (int) $user->id;
		} else {
			$this->modified    = $now;
			$this->modified_by = (int) $user->id;
		}

		if (empty($this->created_by)) {
			$this->created_by = null;
		}
		if (empty($this->modified_by)) {
			$this->modified_by = null;
		}

		return parent::store($updateNulls);
	}
}
