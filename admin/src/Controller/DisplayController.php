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
class DisplayController extends BaseController
{
    protected $default_view = 'cpanel';

    public function migrate(): void
    {
        $this->checkToken();

        $migrationService = new MigrationService();
        $results = $migrationService->migrateAll();

        $message = '<div><h1><strong>Migration abgeschlossen:</strong></h1></div>';

        foreach ($results as $table => $tableResults) {
            $success = array_filter($tableResults, fn($msg) => str_starts_with($msg, '✅'));
            $errors  = array_filter($tableResults, fn($msg) => str_starts_with($msg, '❌'));

            $message .= '<div>';
            $message .= '<div><strong>Tabelle: </strong><span>' . htmlspecialchars($table) . '</span></div>';
            $message .= '<div>';
            $message .= '<span>✅ ' . count($success) . ' erfolgreich</span>';
            $message .= '<span> - </span>';
            if (!empty($errors)) {
                $message .= '<span>❌ ' . count($errors) . ' Fehler</span>';
            } else {
                $message .= '<span>keine Fehler</span>';
            }
            $message .= '</div>';

            // Fehlerdetails als Liste
            if (!empty($errors)) {
                $message .= '<div><strong>Fehlerdetails:</strong><ul>';
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

    public function clean(): void
    {
        $this->checkToken();

        $db = \Joomla\CMS\Factory::getDbo();
        $prefix = $db->getPrefix();

        // Reihenfolge: erst Join-Tabellen, dann abhängige Tabellen, dann Haupttabellen
        $tables = [
            'blaulichtmonitor_einsatz_fahrzeuge',
            'blaulichtmonitor_einsatz_einheiten',
            'blaulichtmonitor_einsatz_kurzbericht',
            'blaulichtmonitor_einsatzleiter_einsatz',
            'blaulichtmonitor_einsatz_presse',
            'blaulichtmonitor_einsatzbilder',
            'blaulichtmonitor_einsaetze',
            'blaulichtmonitor_einheiten',
            'blaulichtmonitor_fahrzeuge',
            'blaulichtmonitor_einsatzleiter_zeitraum',
            'blaulichtmonitor_einsatzleiter',
            'blaulichtmonitor_alarmierungsarten',
            'blaulichtmonitor_dispogruppen',
            'blaulichtmonitor_einsatzarten',
            'blaulichtmonitor_einsatzkategorien',
            'blaulichtmonitor_einsatzort',
            'blaulichtmonitor_kurzbericht',
            'blaulichtmonitor_organisation'
        ];

        $errors = [];
        foreach ($tables as $table) {
            try {
                $db->setQuery('DELETE FROM `' . $prefix . $table . '`');
                $db->execute();
            } catch (\Exception $e) {
                $errors[] = "Fehler beim Leeren von {$table}: " . $e->getMessage();
            }
        }

        if (empty($errors)) {
            $this->app->enqueueMessage('Alle Datenbankeinträge der BlaulichtMonitor-Komponente wurden gelöscht.', 'success');
        } else {
            $this->app->enqueueMessage('Einige Tabellen konnten nicht geleert werden:<br>' . implode('<br>', $errors), 'error');
        }

        $this->setRedirect('index.php?option=com_blaulichtmonitor&view=cpanel');
    }
}
