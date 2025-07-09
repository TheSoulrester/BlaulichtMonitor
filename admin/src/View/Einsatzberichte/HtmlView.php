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
