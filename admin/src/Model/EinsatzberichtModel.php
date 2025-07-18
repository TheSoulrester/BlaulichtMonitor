<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Modell für Einsatzberichte im BlaulichtMonitor.
 * Enthält Methoden zum Laden, Speichern und Bearbeiten von Einsatzberichten inkl. zugehöriger Einheiten, Fahrzeuge und Einsatzorte.
 */
class EinsatzberichtModel extends AdminModel
{
	/**
	 * Lädt das Formular für einen Einsatzbericht.
	 *
	 * @param array $data     Vorbelegte Daten für das Formular.
	 * @param bool  $loadData Soll das Formular mit Daten geladen werden?
	 * @return \JForm|bool    Das Formular-Objekt oder false bei Fehler.
	 */
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

		// Prüfe, ob das Formular geladen werden konnte
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Lädt die Daten für das Einsatzbericht-Formular.
	 * Entweder aus der Session (UserState) oder aus der Datenbank.
	 *
	 * @return array Die Einsatzbericht-Daten.
	 */
	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState(
			'com_blaulichtmonitor.edit.einsatzbericht.data',
			[]
		);

		// Falls keine Daten im UserState, lade aus der Datenbank
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Lädt einen Einsatzbericht inkl. zugehöriger Einheiten, Fahrzeuge, Presseberichte und Einsatzort.
	 *
	 * @param int|null $pk Primärschlüssel des Einsatzberichts.
	 * @return object      Das Einsatzbericht-Objekt.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Lade zugeordnete Einheiten aus der Join-Tabelle
		$query = $db->getQuery(true)
			->select('einheit_id')
			->from($db->quoteName('#__blaulichtmonitor_einsatzberichte_einheiten'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);
		$item->einheiten_id = $db->loadColumn();

		// Lade zugeordnete Fahrzeuge aus der Join-Tabelle
		$query = $db->getQuery(true)
			->select('fahrzeug_id')
			->from($db->quoteName('#__blaulichtmonitor_einsatzberichte_fahrzeuge'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);
		$item->fahrzeuge_id = $db->loadColumn();

		// Lade zugeordnete Presseberichte
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__blaulichtmonitor_einsatzberichte_presse'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int) $item->id);
		$db->setQuery($query);
		$item->presseberichte = $db->loadAssocList();

		// Lade Einsatzort-Daten, falls vorhanden
		if (!empty($item->einsatzort_id)) {
			$query = $db->getQuery(true)
				->select('strasse, hausnummer, plz, stadt')
				->from($db->quoteName('#__blaulichtmonitor_einsatzorte'))
				->where('id = ' . (int)$item->einsatzort_id);
			$db->setQuery($query);
			$einsatzort = $db->loadAssoc();
			if ($einsatzort) {
				$item->einsatzort_strasse    = $einsatzort['strasse'];
				$item->einsatzort_hausnummer = $einsatzort['hausnummer'];
				$item->einsatzort_plz        = $einsatzort['plz'];
				$item->einsatzort_stadt      = $einsatzort['stadt'];
			}
		}

		return $item;
	}

	/**
	 * Speichert einen Einsatzbericht inkl. zugehöriger Einheiten, Fahrzeuge, Einsatzort und Presseberichte.
	 *
	 * @param array $data Die zu speichernden Daten.
	 * @return bool       Erfolg/Misserfolg des Speicherns.
	 */
	public function save($data)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Einsatzort-Daten aus dem Formular holen und ggf. trimmen
		$strasse    = isset($data['einsatzort_strasse']) ? trim($data['einsatzort_strasse']) : '';
		$hausnummer = isset($data['einsatzort_hausnummer']) && $data['einsatzort_hausnummer'] !== '' ? (int)$data['einsatzort_hausnummer'] : null;
		$plz        = isset($data['einsatzort_plz']) ? trim($data['einsatzort_plz']) : null;
		$stadt      = isset($data['einsatzort_stadt']) ? trim($data['einsatzort_stadt']) : '';

		// Einsatzort nur prüfen, wenn mindestens Straße gesetzt ist
		$einsatzortId = null;
		if ($strasse !== '') {
			// Suche nach vorhandenem Einsatzort anhand Straße, Hausnummer, PLZ und Stadt
			$query = $db->getQuery(true)
				->select('id')
				->from($db->quoteName('#__blaulichtmonitor_einsatzorte'))
				->where('strasse = ' . $db->quote($strasse))
				->where('hausnummer ' . ($hausnummer === null ? 'IS NULL' : '= ' . (int)$hausnummer))
				->where('plz ' . ($plz === null ? 'IS NULL' : '= ' . $db->quote($plz)))
				->where('stadt = ' . $db->quote($stadt));
			$db->setQuery($query);
			$einsatzortId = $db->loadResult();

			// Falls Einsatzort nicht vorhanden, neuen Datensatz anlegen
			if (!$einsatzortId) {
				$columns = ['strasse', 'hausnummer', 'plz', 'stadt'];
				$values = [
					$db->quote($strasse),
					$hausnummer === null ? 'NULL' : (int)$hausnummer,
					$plz === null ? 'NULL' : $db->quote($plz),
					$db->quote($stadt)
				];
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__blaulichtmonitor_einsatzorte'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));
				$db->setQuery($query);
				$db->execute();
				$einsatzortId = $db->insertid();
			}
		}

		// Einsatzort-ID im Einsatzbericht speichern
		$data['einsatzort_id'] = $einsatzortId;

		// Einsatzort-Felder aus $data entfernen, damit sie nicht im Einsatzbericht-Datensatz landen
		unset($data['einsatzort_strasse'], $data['einsatzort_hausnummer'], $data['einsatzort_plz'], $data['einsatzort_stadt']);

		// Einheiten und Fahrzeuge aus dem Formular holen (Mehrfachauswahl möglich)
		$einheiten = isset($data['einheiten_id']) ? (array)$data['einheiten_id'] : [];
		$fahrzeuge = isset($data['fahrzeuge_id']) ? (array)$data['fahrzeuge_id'] : [];

		// Presseberichte aus dem Formular holen (Subform: Array von Arrays mit title und url)
		$presseberichte = isset($data['presseberichte']) ? (array)$data['presseberichte'] : [];

		// Felder aus $data entfernen, damit sie nicht im Einsatzbericht-Datensatz landen
		unset($data['einheiten_id'], $data['fahrzeuge_id'], $data['presseberichte']);

		// Einsatzbericht speichern (Standard-Joomla-Methode)
		$result = parent::save($data);

		// Einsatzbericht-ID bestimmen (neu oder bestehend)
		$einsatzberichtId = isset($data['id']) && $data['id'] ? (int)$data['id'] : (int)$this->getState($this->getName() . '.id');
		if (!$einsatzberichtId && $result) {
			$einsatzberichtId = $this->getTable()->id;
		}

		// Einheiten-Zuordnungen aktualisieren:
		// 1. Vorherige Zuordnungen löschen
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__blaulichtmonitor_einsatzberichte_einheiten'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int)$einsatzberichtId);
		$db->setQuery($query);
		$db->execute();

		// 2. Neue Zuordnungen speichern
		if (!empty($einheiten)) {
			foreach ($einheiten as $einheitId) {
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__blaulichtmonitor_einsatzberichte_einheiten'))
					->columns([$db->quoteName('einsatzbericht_id'), $db->quoteName('einheit_id')])
					->values((int)$einsatzberichtId . ',' . (int)$einheitId);
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Fahrzeuge-Zuordnungen aktualisieren:
		// 1. Vorherige Zuordnungen löschen
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__blaulichtmonitor_einsatzberichte_fahrzeuge'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int)$einsatzberichtId);
		$db->setQuery($query);
		$db->execute();

		// 2. Neue Zuordnungen speichern
		if (!empty($fahrzeuge)) {
			foreach ($fahrzeuge as $fahrzeugId) {
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__blaulichtmonitor_einsatzberichte_fahrzeuge'))
					->columns([$db->quoteName('einsatzbericht_id'), $db->quoteName('fahrzeug_id')])
					->values((int)$einsatzberichtId . ',' . (int)$fahrzeugId);
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Presseberichte aktualisieren:
		// 1. Vorherige Presseberichte zum Einsatzbericht löschen
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__blaulichtmonitor_einsatzberichte_presse'))
			->where($db->quoteName('einsatzbericht_id') . ' = ' . (int)$einsatzberichtId);
		$db->setQuery($query);
		$db->execute();

		// 2. Neue Presseberichte speichern
		// Jeder Pressebericht besteht aus title und url
		if (!empty($presseberichte)) {
			foreach ($presseberichte as $presse) {
				$title = isset($presse['title']) ? trim($presse['title']) : '';
				$url   = isset($presse['url']) ? trim($presse['url']) : '';
				if ($title !== '' && $url !== '') {
					$query = $db->getQuery(true)
						->insert($db->quoteName('#__blaulichtmonitor_einsatzberichte_presse'))
						->columns([
							$db->quoteName('einsatzbericht_id'),
							$db->quoteName('title'),
							$db->quoteName('url')
						])
						->values(
							(int)$einsatzberichtId . ', ' .
								$db->quote($title) . ', ' .
								$db->quote($url)
						);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}

		return $result;
	}
}
