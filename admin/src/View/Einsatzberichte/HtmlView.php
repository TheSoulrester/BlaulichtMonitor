<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\View\Einsatzberichte;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View-Klasse für die Einsatzberichte-Übersicht im Backend.
 * Holt Daten vom Model und bereitet sie für das Template auf.
 * Wird von default.php (Template) verwendet.
 * Für weitere Views kann diese Klasse kopiert und angepasst werden.
 */
class HtmlView extends BaseHtmlView
{
	public $filterForm;     // Das Filterformular (Suchfeld, Sortierung, Limit)
	public $state;          // State-Objekt mit Filter- und Sortierinformationen
	public $items = [];     // Die Einsatzberichte-Datensätze
	public $pagination;     // Paginierungsobjekt
	public $activeFilters = []; // Aktive Filter

	/**
	 * Lädt alle benötigten Daten vom Model und gibt sie an das Template weiter.
	 * Wird automatisch von Joomla aufgerufen.
	 */
	public function display($tpl = null): void
	{
		/** @var ContactsModel $model */
		$model = $this->getModel();

		$this->state         = $model->getState();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$errors              = $model->getErrors();
		if (\is_array($errors) && \count($errors)) {
			throw new GenericDataException(implode(
				'\n',
				$errors
			), 500);
		}
		parent::display($tpl);
	}
}
