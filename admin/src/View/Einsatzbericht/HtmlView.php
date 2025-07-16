<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\View\Einsatzbericht;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	public $form;
	public $state;
	public $item;

	public function display($tpl = null): void
	{
		$this->form  = $this->get('Form');
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$errors      = $this->get('Errors');
		if (\is_array($errors) && \count($errors)) {
			throw new GenericDataException(implode(
				"\n",
				$errors
			), 500);
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$isNew   = ($this->item->id == 0);
		$canDo   = ContentHelper::getActions('com_blaulichtmonitor');
		$toolbar = Toolbar::getInstance();
		ToolbarHelper::title(
			Text::_('COM_BLAULICHTMONITOR_EINSATZBERICHT_TITLE_' . ($isNew ? 'ADD' : 'EDIT'))
		);
		if ($canDo->get('core.create')) {
			if ($isNew) {
				$toolbar->apply('einsatzbericht.apply');
			} else {
				$toolbar->apply('einsatzbericht.apply');
			}
			$toolbar->save('einsatzbericht.save');
		}
		$toolbar->cancel('einsatzbericht.cancel', 'JTOOLBAR_CLOSE');
	}
}
