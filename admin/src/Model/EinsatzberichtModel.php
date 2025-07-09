<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

class EinsatzberichtModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_blaulichtmonitor.einsatzbericht',
            'einsatzbericht',
            [
                'control'   => 'jform',
                'load_data' => $loadData,
            ]
        );
        if (empty($form)) {
            return false;
        }
        return $form;
    }
    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState(
            'com_blaulichtmonitor.edit.einsatzbericht.data',
            []
        );
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}
