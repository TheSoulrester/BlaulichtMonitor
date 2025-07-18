<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;

/**
 * Table-Klasse für Einsatzberichte.
 * Diese Klasse kapselt die Datenbankoperationen für die Einsatzberichte-Tabelle
 * und sorgt für die korrekte Vorverarbeitung und Speicherung der Daten.
 */
class EinsatzberichtTable extends Table
{
	/**
	 * Konstruktor: Initialisiert die Tabelle mit Name und Primärschlüssel.
	 *
	 * @param DatabaseDriver $db Datenbanktreiber-Objekt
	 */
	public function __construct(DatabaseDriver $db)
	{
		// Initialisiere die Tabelle mit dem Namen und dem Primärschlüssel-Feld
		parent::__construct('#__blaulichtmonitor_einsatzberichte', 'id', $db);
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
		// Setze leere Datumsfelder (z.B. aus dem Formular) auf null, damit sie nicht als leere Strings gespeichert werden
		foreach (['alarmierungszeit', 'ausrueckzeit', 'einsatzende'] as $field) {
			if (isset($array[$field]) && $array[$field] === '') {
				$array[$field] = null;
			}
		}

		// Setze leere Zahlenfelder (z.B. Personenanzahl) auf null
		foreach (['people_count'] as $field) {
			if (isset($array[$field]) && $array[$field] === '') {
				$array[$field] = null;
			}
		}

		// Übergibt die vorbereiteten Daten an die Joomla-Table-Methode
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
		$user = Factory::getApplication()->getIdentity(); // Hole aktuellen Benutzer
		$now  = Factory::getDate()->toSql();              // Hole aktuellen Zeitstempel

		if ($this->id) {
			// Datensatz wird aktualisiert: Änderungsdatum und Benutzer setzen
			$this->modified    = $now;
			$this->modified_by = (int) $user->id;
		} else {
			// Neuer Datensatz: Erstellungsdatum und Benutzer setzen
			$this->created    = $now;
			$this->created_by = (int) $user->id;
		}

		// Falls Benutzerfelder leer sind, auf NULL setzen (Datenbankverträglichkeit)
		if (empty($this->created_by)) {
			$this->created_by = null;
		}
		if (empty($this->modified_by)) {
			$this->modified_by = null;
		}

		// Speichere den Datensatz in der Datenbank
		return parent::store($updateNulls);
	}
}
