<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\View\Alarmierungsart;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View-Klasse für die Einzelansicht einer Alarmierungsart.
 * Stellt das Formular dar und konfiguriert die Toolbar für die Bearbeitung.
 */
class HtmlView extends BaseHtmlView
{
	/** @var \JForm Formularobjekt für die Alarmierungsart */
	public $form;

	/** @var object State-Objekt mit Statusinformationen */
	public $state;

	/** @var object Alarmierungsart-Datensatz */
	public $item;

	/**
	 * Anzeige der Einzelansicht (Formular) für eine Alarmierungsart.
	 * Holt die benötigten Daten, prüft auf Fehler und baut die Toolbar.
	 *
	 * @param string|null $tpl Optionaler Template-Name
	 * @throws GenericDataException Bei Fehlern im Datenmodell
	 */
	public function display($tpl = null): void
	{
		// Lade Formular, Status und Alarmierungsart-Daten aus dem Model
		$this->form  = $this->get('Form');
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');

		// Prüfe, ob Fehler im Model vorliegen und werfe ggf. Exception
		$errors = $this->get('Errors');
		if (\is_array($errors) && \count($errors)) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Toolbar für die Bearbeitung hinzufügen
		$this->addToolbar();

		// Zeige das Formular-Template an
		parent::display($tpl);
	}

	/**
	 * Konfiguriert die Toolbar für die Alarmierungsart-Bearbeitung.
	 * Zeigt je nach Berechtigung die passenden Buttons an.
	 */
	protected function addToolbar()
	{
		// Blende das Hauptmenü aus, um Fokus auf das Formular zu legen
		Factory::getApplication()->input->set('hidemainmenu', true);

		// Prüfe, ob es sich um einen neuen Datensatz handelt
		$isNew = ($this->item->id == 0);

		// Ermittle die Berechtigungen des aktuellen Benutzers
		$canDo = ContentHelper::getActions('com_blaulichtmonitor');

		// Hole die Toolbar-Instanz
		$toolbar = Toolbar::getInstance();

		// Setze den Titel der Toolbar abhängig vom Modus (Neu/Bearbeiten)
		ToolbarHelper::title(
			Text::_('COM_BLAULICHTMONITOR_ALARMIERUNGSART_TITLE_' . ($isNew ? 'ADD' : 'EDIT'))
		);

		// Zeige die Buttons zum Speichern und Anwenden, falls Berechtigung vorhanden
		if ($canDo->get('core.create')) {
			$toolbar->apply('alarmierungsart.apply');
			$toolbar->save('alarmierungsart.save');
		}

		// Button zum Abbrechen/Schließen der Bearbeitung
		$toolbar->cancel('alarmierungsart.cancel', 'JTOOLBAR_CLOSE');
	}
}
