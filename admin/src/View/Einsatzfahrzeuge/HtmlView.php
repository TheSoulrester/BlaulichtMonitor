<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\View\Einsatzfahrzeuge;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View-Klasse für die Einsatzarten-Übersicht im Backend.
 * Holt Daten vom Model und bereitet sie für das Template auf.
 * Wird von default.php (Template) verwendet.
 * Für weitere Views kann diese Klasse kopiert und angepasst werden.
 */
class HtmlView extends BaseHtmlView
{
	public $filterForm;     // Das Filterformular (Suchfeld, Sortierung, Limit)
	public $state;          // State-Objekt mit Filter- und Sortierinformationen
	public $items = [];     // Die Alarmierungsarten-Datensätze
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

		// ...existing code...
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}
		parent::display($tpl);

		$user = Factory::getApplication()->getIdentity();
		if (!$user->authorise('core.manage', 'com_blaulichtmonitor')) {
			throw new GenericDataException('Not allowed', 403);
		}
	}

	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_blaulichtmonitor');
		ToolbarHelper::title('Einsatzfahrzeuge', 'copy article');

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew('einsatzfahrzeug.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('einsatzfahrzeug.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {
			ToolbarHelper::publish('einsatzfahrzeuge.publish', 'JTOOLBAR_PUBLISH', true);
			ToolbarHelper::unpublish('einsatzfahrzeuge.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolbarHelper::archiveList('einsatzfahrzeuge.archive', 'JTOOLBAR_ARCHIVE');
			ToolbarHelper::checkin('einsatzfahrzeuge.checkin');
		}
		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'einsatzfahrzeuge.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_blaulichtmonitor');
		}
		//ToolbarHelper::help('Alarmierungsarten'); // Hilfe-Button hinzufügen mit eigenen Link?
	}
}
