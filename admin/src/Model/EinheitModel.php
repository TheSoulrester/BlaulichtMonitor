<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Modell für Einheiten im BlaulichtMonitor.
 * Enthält Methoden zum Laden, Speichern und Bearbeiten von Einheiten.
 */
class EinheitModel extends AdminModel
{
	/**
	 * Lädt das Formular für eine Einheit.
	 *
	 * @param array $data     Vorbelegte Daten für das Formular.
	 * @param bool  $loadData Soll das Formular mit Daten geladen werden?
	 * @return \JForm|bool    Das Formular-Objekt oder false bei Fehler.
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_blaulichtmonitor.einheit',
			'einheit',
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
	 * Lädt die Daten für das Einheit-Formular.
	 * Entweder aus der Session (UserState) oder aus der Datenbank.
	 *
	 * @return array Die Einheit-Daten.
	 */
	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState(
			'com_blaulichtmonitor.edit.einheit.data',
			[]
		);

		// Falls keine Daten im UserState, lade aus der Datenbank
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Lädt eine Einheit aus der Datenbank.
	 *
	 * @param int|null $pk Primärschlüssel der Einheit.
	 * @return object      Das Einheit-Objekt.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Lade zusätzliche Daten hier, falls erforderlich

		return $item;
	}

	/**
	 * Speichert eine Einheit in der Datenbank.
	 *
	 * @param array $data Die zu speichernden Daten.
	 * @return bool       Erfolg/Misserfolg des Speicherns.
	 */
	public function save($data)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Daten aus dem Formular holen und ggf. trimmen
		$title = isset($data['title']) ? trim($data['title']) : '';

		// Validierung: Titel darf nicht leer sein
		if ($title === '') {
			throw new \InvalidArgumentException('Der Titel der Einheit darf nicht leer sein.');
		}

		// Einheit-Daten in die Datenbank speichern
		$result = parent::save($data);

		return $result;
	}
}
