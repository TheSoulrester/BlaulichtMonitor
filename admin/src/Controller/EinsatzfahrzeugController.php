<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller-Klasse für Alarmierungsarten.
 * Steuert die Formularaktionen für Alarmierungsarten.
 */
class EinsatzfahrzeugController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'einsatzfahrzeuge';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'einsatzfahrzeug';

	// Keine speziellen AJAX-Methoden oder Zusatzfunktionen
}
