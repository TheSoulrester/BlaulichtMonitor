<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Modell für Einsatzkategorien im BlaulichtMonitor.
 * Enthält Methoden zum Laden, Speichern und Bearbeiten von Einsatzkategorien.
 */
class EinsatzkategorieModel extends AdminModel
{
	/**
	 * Lädt das Formular für eine Einsatzkategorien.
	 *
	 * @param array $data     Vorbelegte Daten für das Formular.
	 * @param bool  $loadData Soll das Formular mit Daten geladen werden?
	 * @return \JForm|bool    Das Formular-Objekt oder false bei Fehler.
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_blaulichtmonitor.einsatzkategorie',
			'einsatzkategorie',
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
	 * Lädt die Daten für das Einsatzkategorien-Formular.
	 * Entweder aus der Session (UserState) oder aus der Datenbank.
	 *
	 * @return array Die Einsatzkategorien-Daten.
	 */
	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState(
			'com_blaulichtmonitor.edit.einsatzkategorien.data',
			[]
		);

		// Falls keine Daten im UserState, lade aus der Datenbank
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Lädt eine Einsatzart aus der Datenbank.
	 *
	 * @param int|null $pk Primärschlüssel der Einsatzart.
	 * @return object      Das Einsatzart-Objekt.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Lade zusätzliche Daten hier, falls erforderlich

		return $item;
	}

	/**
	 * Speichert eine Einsatzart in der Datenbank.
	 *
	 * @param array $data Die zu speichernden Daten.
	 * @return bool       Erfolg/Misserfolg des Speicherns.
	 */
	public function save($data)
	{
		if (isset($data['icon_url']) && is_array($data['icon_url'])) {
        $rawPath = $data['icon_url']['imagefile'] ?? '';
        $data['icon_url'] = explode('#', $rawPath)[0];
    }
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Daten aus dem Formular holen und ggf. trimmen
		$title = isset($data['title']) ? trim($data['title']) : '';

		// Validierung: Titel darf nicht leer sein
		if ($title === '') {
			throw new \InvalidArgumentException('Der Titel der Einsatzkategorie darf nicht leer sein.');
		}

		// Alarmierungsart-Daten in die Datenbank speichern
		$result = parent::save($data);

		return $result;
	}
}
