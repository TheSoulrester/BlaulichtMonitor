<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use AlexanderGropp\Component\BlaulichtMonitor\Administrator\Service\MigrationService;


defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_blaulichtmonitor
 *
 * @copyright   Copyright (C) 2025 Alexander Gropp. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Default Controller of BlaulichtMonitor component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_blaulichtmonitor
 */
class DisplayController extends BaseController
{
    /**
     * The default view for the display method.
     *
     * @var string
     */
    protected $default_view = 'cpanel';

    public function migrate(): void
    {
        $this->checkToken(); // oder checkToken('post'), je nachdem wie du aufrufst

        $migrationService = new MigrationService();
        $results = $migrationService->migrateEinsatzarten();

        $this->app->enqueueMessage("Migration abgeschlossen:<br>" . implode('<br>', $results), 'message');
        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
    }
}
