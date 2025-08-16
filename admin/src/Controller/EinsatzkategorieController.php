<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller-Klasse für Einsatzkategorien.
 * Steuert die Formularaktionen für Einsatzkategorien.
 */
class EinsatzkategorieController extends FormController
{
	/**
	 * Name der Listenansicht (Plural) für Redirects.
	 * Wird z.B. nach Speichern oder Abbrechen verwendet.
	 */
	protected $view_list = 'einsatzkategorien';

	/**
	 * Name der Einzelansicht (Singular) für Redirects.
	 */
	protected $view_item = 'einsatzkategorie';

	// Keine speziellen AJAX-Methoden oder Zusatzfunktionen
}
