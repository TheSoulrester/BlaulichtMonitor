<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

class EinsatzberichtController extends FormController
{
    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=einsatzberichte');
    }

    protected function getRedirectToListAppend()
    {
        // Wird nach save/apply verwendet
        return '';
    }

    protected function getRedirectToListRoute($append = null)
    {
        return 'index.php?option=com_blaulichtmonitor&view=einsatzberichte' . ($append ? '&' . $append : '');
    }
}
