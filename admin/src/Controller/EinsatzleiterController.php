<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller-Klasse für Einsatzleiter.
 * Steuert die Formularaktionen für Einsatzleiter.
 */
class EinsatzleiterController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'einsatzleiterliste';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'einsatzleiter';

	// Keine speziellen AJAX-Methoden oder Zusatzfunktionen
}
