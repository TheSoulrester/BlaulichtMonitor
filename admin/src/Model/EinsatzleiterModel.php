<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Modell für Einsatzleiter im BlaulichtMonitor.
 * Enthält Methoden zum Laden, Speichern und Bearbeiten von Einsatzleiter.
 */
class EinsatzleiterModel extends AdminModel
{
	/**
	 * Lädt das Formular für eine Einsatzleiter.
	 *
	 * @param array $data     Vorbelegte Daten für das Formular.
	 * @param bool  $loadData Soll das Formular mit Daten geladen werden?
	 * @return \JForm|bool    Das Formular-Objekt oder false bei Fehler.
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_blaulichtmonitor.einsatzleiter',
			'einsatzleiter',
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
	 * Lädt die Daten für das Einsatzleiter-Formular.
	 * Entweder aus der Session (UserState) oder aus der Datenbank.
	 *
	 * @return array Die Einsatzleiter-Daten.
	 */
	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState(
			'com_blaulichtmonitor.edit.einsatzleiter.data',
			[]
		);

		// Falls keine Daten im UserState, lade aus der Datenbank
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Lädt eine Einsatzleiter aus der Datenbank.
	 *
	 * @param int|null $pk Primärschlüssel der Einsatzleiter.
	 * @return object      Das Einsatzleiter-Objekt.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Lade zusätzliche Daten hier, falls erforderlich

		return $item;
	}

	/**
	 * Speichert eine Einsatzleiter in der Datenbank.
	 *
	 * @param array $data Die zu speichernden Daten.
	 * @return bool       Erfolg/Misserfolg des Speicherns.
	 */
	public function save($data)
	{
		
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Daten aus dem Formular holen und ggf. trimmen
		$name = isset($data['name']) ? trim($data['name']) : '';

		// Validierung: Titel darf nicht leer sein
		if ($name === '') {
			throw new \InvalidArgumentException('Der Name des Einsatzleiters darf nicht leer sein.');
		}

		// Einsatzleiter-Daten in die Datenbank speichern
		$result = parent::save($data);

		return $result;
	}
}
