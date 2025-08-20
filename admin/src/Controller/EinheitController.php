<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller-Klasse für Einheiten.
 * Steuert die Formularaktionen für Einheiten.
 */
class EinheitController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'einheiten';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'einheit';

	// Keine speziellen AJAX-Methoden oder Zusatzfunktionen
}
