<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('form.token');

$link = Route::_('index.php?option=com_blaulichtmonitor&task=display.migrate&' . Session::getFormToken() . '=1');
?>

<div class="container-fluid">
    <h2 class="mb-4">🚨 BlaulichtMonitor – Control Panel</h2>
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-dark fw-bold">
            🔧 Werkzeuge
        </div>
        <div class="card-body">
            <h4 class="card-title">🚧 Migration starten</h4>
            <p class="card-text">
                Hier kannst du die Daten aus der alten <code>eiko_</code>-Komponente in das neue Schema übernehmen.
            </p>
            <div class="d-flex gap-2 mt-3">
                <form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.migrate'); ?>" method="post" class="mt-3">
                    <?= HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-warning"
                        onclick="return confirm('Willst du wirklich die Migration starten?');">
                        🔄 Migration starten
                    </button>
                </form>
                <form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.cleantables'); ?>" method="post" class="mt-3">
                    <?= HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Willst du wirklich ALLE Daten der BlaulichtMonitor-Komponente löschen? Dieser Vorgang kann nicht rückgängig gemacht werden!');">
                        🧹 Alle Datenbankeinträge löschen
                    </button>
                </form>
                <form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.droptables'); ?>" method="post" class="mt-3">
                    <?= HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Willst du wirklich ALLE Tabellen der BlaulichtMonitor-Komponente löschen? Dieser Vorgang kann nicht rückgängig gemacht werden!');">
                        🗑️ Tabellen löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>