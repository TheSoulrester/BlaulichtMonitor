<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller-Klasse für Organisationen.
 * Steuert die Formularaktionen für Organisationen.
 */
class OrganisationController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'organisationen';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'organisation';

	// Keine speziellen AJAX-Methoden oder Zusatzfunktionen
}
