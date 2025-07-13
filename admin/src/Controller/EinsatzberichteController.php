<?php

/**
 * @package     com_blaulichtmonitor
 */

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class EinsatzberichteController extends AdminController
{
	/**
	 * Prefix für Sprachstrings.
	 *
	 * @var string
	 */
	protected $text_prefix = 'COM_BLAULICHTMONITOR_EINSATZBERICHTE';

	public function __construct($config = [], ?MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Proxy für getModel.
	 *
	 * @param   string  $name    Der Modelname. Optional.
	 * @param   string  $prefix  Der Klassenprefix. Optional.
	 * @param   array   $config  Konfiguration. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 */
	public function getModel($name = 'Einsatzbericht', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	// Beispiel für einen eigenen Task (nur falls benötigt):
	/*
    public function featured()
    {
        // Hier könntest du analog zu com_content einen eigenen Task implementieren,
        // z.B. um Einsatzberichte als "besonders" zu markieren.
    }
    */

	// Beispiel für einen eigenen JSON-Task (nur falls benötigt):
	/*
    public function getQuickiconContent()
    {
        // Hier könntest du z.B. eine Statistik als JSON ausgeben.
    }
    */
}
