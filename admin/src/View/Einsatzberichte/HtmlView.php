<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\View\Einsatzberichte;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    public $filterForm;
    public $state;
    public $items = [];
    public $pagination;
    public $activeFilters = [];
    public function display($tpl = null): void
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $errors = $this->get('Errors');
        if (is_array($errors) && count($errors)) {
            throw new GenericDataException(implode(
                '\n',
                $errors
            ), 500);
        }
        parent::display($tpl);
    }
}
