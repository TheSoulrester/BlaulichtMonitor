<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

class EinsatzberichtController extends FormController
{
	protected $view_list = 'einsatzberichte';
	protected $view_item = 'einsatzbericht';

	/* wird vorerst nicht benötigt

	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_blaulichtmonitor&view=' . $this->view_list);
	}

	protected function getRedirectToListAppend()
	{
		// Wird nach save/apply verwendet
		return '';
	}

	protected function getRedirectToListRoute($append = null)
	{
		return 'index.php?option=com_blaulichtmonitor&view=' . $this->view_list . ($append ? '&' . $append : '');
	}
	*/
}
