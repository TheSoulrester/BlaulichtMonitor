<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

/**
 * Controller-Klasse für Einsatzberichte.
 * Steuert die Formularaktionen und spezielle AJAX-Abfragen für Einsatzorte.
 */
class EinsatzberichtController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'einsatzberichte';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'einsatzbericht';

	/*
    // Die folgenden Methoden sind Standard-Redirects und werden aktuell nicht benötigt.
    // Sie können bei Bedarf aktiviert und angepasst werden.

    // Bricht die Bearbeitung ab und leitet zurück zur Listenansicht.
    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=' . $this->view_list);
    }

    // Gibt einen zusätzlichen String für die Redirect-URL zurück (z.B. Filter).
    protected function getRedirectToListAppend()
    {
        // Wird nach save/apply verwendet
        return '';
    }

    // Baut die Redirect-URL zur Listenansicht zusammen.
    protected function getRedirectToListRoute($append = null)
    {
        return 'index.php?option=com_blaulichtmonitor&view=' . $this->view_list . ($append ? '&' . $append : '');
    }
    */

	/**
	 * AJAX-Methode: Gibt die Einsatzort-Daten als JSON zurück.
	 * Wird z.B. für dynamische Formularfelder oder Detailanzeigen verwendet.
	 *
	 * Erwartet: $_GET['id'] mit der Einsatzort-ID.
	 * Antwort: JSON-Objekt mit den Einsatzort-Daten.
	 */
	public function getEinsatzort()
	{
		// Hole die Einsatzort-ID aus der Anfrage
		$id = $this->input->getInt('id');

		// Hole den Datenbanktreiber
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Baue die Abfrage für den gewünschten Einsatzort
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__blaulichtmonitor_einsatzorte'))
			->where('id = ' . (int)$id);

		// Führe die Abfrage aus und hole die Daten als assoziatives Array
		$db->setQuery($query);
		$ort = $db->loadAssoc();

		// Sende die Daten als JSON an den Client (z.B. für ein AJAX-Formular)
		echo json_encode($ort);

		// Beende die Joomla-Anwendung, damit keine weitere Ausgabe erfolgt
		$this->app->close();
	}
}
