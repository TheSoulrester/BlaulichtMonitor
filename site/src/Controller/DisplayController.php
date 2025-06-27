<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

/**
 * @package     Joomla.Site
 * @subpackage  com_blaulichtmonitor
 *
 * @copyright   Copyright (C) 2025 Alexander Gropp. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * BlaulichtMonitor Component Controller
 * @since  0.0.2
 */
class DisplayController extends BaseController
{

    public function display($cachable = false, $urlparams = array())
    {
        // NEW DECLARATION => https://api.joomla.org/cms-5/deprecated.html
        //$app = Factory::getApplication();
        //$document = $app->getDocument();
        $document = Factory::getDocument();
        //
        $viewName = $this->input->getCmd('view', 'login');
        $viewFormat = $document->getType();

        $view = $this->getView($viewName, $viewFormat);

        $view->document = $document;
        $view->display();
    }
}
