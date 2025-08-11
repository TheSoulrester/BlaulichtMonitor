<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

HTMLHelper::_('form.token');

$link = Route::_('index.php?option=com_blaulichtmonitor&task=display.migrate&' . Session::getFormToken() . '=1');

// Tabellen-Check für die Einsatzkomponente (eiko_)
$db = Factory::getContainer()->get('DatabaseDriver');
$prefix = $db->getPrefix();
$requiredTables = [
	$prefix . 'eiko_alarmierungsarten',
	$prefix . 'eiko_einsatzarten',
	$prefix . 'eiko_einsatzberichte',
	$prefix . 'eiko_fahrzeuge',
	$prefix . 'eiko_organisationen',
	// ggf. weitere Tabellen ergänzen
];
$missingTables = [];
foreach ($requiredTables as $table) {
	$db->setQuery("SHOW TABLES LIKE " . $db->quote($table));
	if (!$db->loadResult()) {
		$missingTables[] = $table;
	}
}
$canMigrate = empty($missingTables);
?>

<div class="container-fluid">
	<h2 class="mb-4"><?= Text::_('COM_BLAULICHTMONITOR_CPANEL_TITLE'); ?></h2>
	<div class="card border-warning mb-4">
		<div class="card-header bg-warning text-dark fw-bold">
			<?= Text::_('COM_BLAULICHTMONITOR_TOOLS'); ?>
		</div>
		<div class="card-body">
			<h4 class="card-title"><?= Text::_('COM_BLAULICHTMONITOR_MIGRATION_START'); ?></h4>
			<p class="card-text">
				<?= Text::_('COM_BLAULICHTMONITOR_MIGRATION_DESC'); ?>
			</p>
			<?php if (!$canMigrate): ?>
				<div class="alert alert-warning mb-3 py-2 px-3 small shadow-sm" style="max-width: 500px;">
					<strong class="text-dark"><?= Text::_('Mindestens eine benötigte Tabelle der Einsatzkomponente fehlt:'); ?></strong>
					<ul class="mb-0 ps-3 text-dark" style="text-align:left; list-style-type: disc;">
						<?php foreach ($missingTables as $tbl): ?>
							<li><code class="text-body"><?= htmlspecialchars($tbl) ?></code></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="d-flex gap-3 mt-3 flex-wrap justify-content-start">
				<!-- Migration-Button -->
				<form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.migrate'); ?>" method="post">
					<?= HTMLHelper::_('form.token'); ?>
					<button type="submit" class="btn btn-warning"
						onclick="return confirm('<?= Text::_('COM_BLAULICHTMONITOR_MIGRATION_CONFIRM'); ?>');"
						<?= $canMigrate ? '' : 'disabled' ?>>
						<?= Text::_('COM_BLAULICHTMONITOR_MIGRATION_START_BUTTON'); ?>
						<?php if (!$canMigrate): ?>
							<span class="ms-1 text-danger" style="vertical-align:middle;">
								<i class="bi bi-exclamation-circle-fill"></i>
							</span>
						<?php endif; ?>
					</button>
				</form>
				<!-- Clean Button -->
				<form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.cleantables'); ?>" method="post">
					<?= HTMLHelper::_('form.token'); ?>
					<button type="submit" class="btn btn-danger"
						onclick="return confirm('<?= Text::_('COM_BLAULICHTMONITOR_CLEAN_CONFIRM'); ?>');">
						<?= Text::_('COM_BLAULICHTMONITOR_CLEAN_BUTTON'); ?>
					</button>
				</form>
				<!-- Drop Button -->
				<form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.droptables'); ?>" method="post">
					<?= HTMLHelper::_('form.token'); ?>
					<button type="submit" class="btn btn-danger"
						onclick="return confirm('<?= Text::_('COM_BLAULICHTMONITOR_DROP_CONFIRM'); ?>');">
						<?= Text::_('COM_BLAULICHTMONITOR_DROP_BUTTON'); ?>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
