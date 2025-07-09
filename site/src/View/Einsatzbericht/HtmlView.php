<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Site\View\Einsatzbericht;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    public $item;

    public function display($tpl = null): void
    {
        $this->item = $this->get('Item');
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode(
                "\n",
                $errors
            ), 500);
        }
        parent::display($tpl);
    }
}
