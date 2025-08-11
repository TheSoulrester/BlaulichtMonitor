<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller-Klasse für Alarmierungsarten.
 * Steuert die Formularaktionen für Alarmierungsarten.
 */
class AlarmierungsartController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'alarmierungsarten';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'alarmierungsart';

	// Keine speziellen AJAX-Methoden oder Zusatzfunktionen
}
