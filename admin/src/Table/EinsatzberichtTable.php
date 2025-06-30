<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class EinsatzberichtTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__blaulichtmonitor_einsaetze', 'id', $db);
    }
}
