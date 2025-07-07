<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Controller;

defined('_JEXEC') or die;

use AlexanderGropp\Component\BlaulichtMonitor\Administrator\Service\MigrationService;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_blaulichtmonitor
 *
 * @copyright   Copyright (C) 2025 Alexander Gropp. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */
class DisplayController extends BaseController
{
    protected $default_view = 'cpanel';

    public function migrate(): void
    {
        $this->checkToken();

        $migrationService = new MigrationService();
        $results = $migrationService->migrateAll();

        $message = '<div><h1><strong>' . Text::_('COM_BLAULICHTMONITOR_MIGRATION_COMPLETED') . '</strong></h1></div>';

        foreach ($results as $table => $tableResults) {
            $success = array_filter($tableResults, fn($msg) => str_starts_with($msg, '✅'));
            $errors  = array_filter($tableResults, fn($msg) => str_starts_with($msg, '❌'));

            $message .= '<div>';
            $message .= '<div><strong>' . Text::_('COM_BLAULICHTMONITOR_TABLE') . '</strong><span>' . htmlspecialchars($table) . '</span></div>';
            $message .= '<div>';
            $message .= '<span>✅ ' . count($success) . Text::_('COM_BLAULICHTMONITOR_SUCCESS') . '</span>';
            $message .= '<span> - </span>';
            if (!empty($errors)) {
                $message .= '<span>❌ ' . count($errors) . Text::_('COM_BLAULICHTMONITOR_ERROR') . '</span>';
            } else {
                $message .= '<span>' . Text::_('COM_BLAULICHTMONITOR_NO_ERRORS') . '</span>';
            }
            $message .= '</div>';

            // Fehlerdetails als Liste
            if (!empty($errors)) {
                $message .= '<div><strong>' . Text::_('COM_BLAULICHTMONITOR_ERROR_DETAILS') . '</strong><ul>';
                foreach ($errors as $err) {
                    $message .= '<li>' . htmlspecialchars($err) . '</li>';
                }
                $message .= '</ul></div>';
            }

            $message .= '</div>';
        }

        // Joomla unterstützt HTML in 'none' Messages, aber kein JS/CSS für Interaktivität!
        $this->app->enqueueMessage($message, 'none');
        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
    }

    public function cleanTables(): void
    {
        $this->checkToken();

        $db = \Joomla\CMS\Factory::getDbo();
        $prefix = $db->getPrefix();

        // Alle Tabellen mit "blaulichtmonitor" im Namen suchen
        $db->setQuery("SHOW TABLES LIKE " . $db->quote($prefix . '%blaulichtmonitor%'));
        $tables = $db->loadColumn();

        if (empty($tables)) {
            $this->app->enqueueMessage(Text::_('COM_BLAULICHTMONITOR_NO_TABLES_FOUND'), 'info');
            $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
            return;
        }

        $errors = [];
        $successTables = [];
        foreach ($tables as $table) {
            try {
                $db->setQuery('DELETE FROM `' . $table . '`');
                $db->execute();
                $successTables[] = $table;
            } catch (\Exception $e) {
                $errors[] = Text::sprintf('COM_BLAULICHTMONITOR_TABLES_CLEAN_ERROR_DETAIL', $table, $e->getMessage());
            }
        }

        if (!empty($successTables)) {
            $tableList = '<ul>';
            foreach ($successTables as $table) {
                $tableList .= '<li>' . htmlspecialchars($table) . '</li>';
            }
            $tableList .= '</ul>';
            $this->app->enqueueMessage(
                '<strong>' . Text::_('COM_BLAULICHTMONITOR_TABLES_CLEANED') . '</strong>' . $tableList,
                'success'
            );
        }

        if (!empty($errors)) {
            $this->app->enqueueMessage(Text::_('COM_BLAULICHTMONITOR_TABLES_CLEAN_ERROR') . '<br>' . implode('<br>', $errors), 'error');
        }

        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
    }

    public function droptables(): void
    {
        $this->checkToken();

        $db = \Joomla\CMS\Factory::getDbo();
        $prefix = $db->getPrefix();

        // Alle Tabellen mit "blaulichtmonitor" im Namen suchen
        $db->setQuery("SHOW TABLES LIKE " . $db->quote($prefix . '%blaulichtmonitor%'));
        $tables = $db->loadColumn();

        if (empty($tables)) {
            $this->app->enqueueMessage(Text::_('COM_BLAULICHTMONITOR_NO_TABLES_FOUND') . ' ' . Text::_('COM_BLAULICHTMONITOR_NO_TABLES_DROPPED'), 'info');
            $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
            return;
        }

        $successTables = [];
        $errors = [];
        foreach ($tables as $table) {
            try {
                $db->setQuery('DROP TABLE IF EXISTS `' . $table . '`');
                $db->execute();
                $successTables[] = $table;
            } catch (\Exception $e) {
                $errors[] = Text::sprintf('COM_BLAULICHTMONITOR_TABLES_DROP_ERROR_DETAIL', $table, $e->getMessage());
            }
        }

        if (!empty($successTables)) {
            $tableList = '<ul>';
            foreach ($successTables as $table) {
                $tableList .= '<li>' . htmlspecialchars($table) . '</li>';
            }
            $tableList .= '</ul>';
            $this->app->enqueueMessage(
                '<strong>' . Text::_('COM_BLAULICHTMONITOR_TABLES_DROPPED') . '</strong>' . $tableList,
                'success'
            );
        }

        if (!empty($errors)) {
            $this->app->enqueueMessage(Text::_('COM_BLAULICHTMONITOR_TABLES_DROP_ERROR') . '<br>' . implode('<br>', $errors), 'error');
        }

        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
    }
}
