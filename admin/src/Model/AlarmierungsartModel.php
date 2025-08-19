<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Modell für Alarmierungsarten im BlaulichtMonitor.
 * Enthält Methoden zum Laden, Speichern und Bearbeiten von Alarmierungsarten.
 */
class AlarmierungsartModel extends AdminModel
{
	/**
	 * Lädt das Formular für eine Alarmierungsart.
	 *
	 * @param array $data     Vorbelegte Daten für das Formular.
	 * @param bool  $loadData Soll das Formular mit Daten geladen werden?
	 * @return \JForm|bool    Das Formular-Objekt oder false bei Fehler.
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_blaulichtmonitor.alarmierungsart',
			'alarmierungsart',
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
	 * Lädt die Daten für das Alarmierungsart-Formular.
	 * Entweder aus der Session (UserState) oder aus der Datenbank.
	 *
	 * @return array Die Alarmierungsart-Daten.
	 */
	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState(
			'com_blaulichtmonitor.edit.alarmierungsart.data',
			[]
		);

		// Falls keine Daten im UserState, lade aus der Datenbank
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Lädt eine Alarmierungsart aus der Datenbank.
	 *
	 * @param int|null $pk Primärschlüssel der Alarmierungsart.
	 * @return object      Das Alarmierungsart-Objekt.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Lade zusätzliche Daten hier, falls erforderlich

		return $item;
	}

	/**
	 * Speichert eine Alarmierungsart in der Datenbank.
	 *
	 * @param array $data Die zu speichernden Daten.
	 * @return bool       Erfolg/Misserfolg des Speicherns.
	 */
	public function save($data)
	{
		if (isset($data['image_url']) && is_array($data['image_url'])) {
        $rawPath = $data['image_url']['imagefile'] ?? '';
        $data['image_url'] = explode('#', $rawPath)[0];
			
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Daten aus dem Formular holen und ggf. trimmen
		$title = isset($data['title']) ? trim($data['title']) : '';

		// Validierung: Titel darf nicht leer sein
		if ($title === '') {
			throw new \InvalidArgumentException('Der Titel der Alarmierungsart darf nicht leer sein.');
		}

		// Alarmierungsart-Daten in die Datenbank speichern
		$result = parent::save($data);

		return $result;
	}
}
