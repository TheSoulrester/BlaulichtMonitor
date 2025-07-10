<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/**
 * Template für die Einsatzberichte-Übersicht im Backend.
 * Stellt das HTML für die Listenansicht bereit, inklusive Filterformular, Tabelle und Pagination.
 * Nutzt Daten aus dem View (HtmlView).
 * Für weitere Views kann dieses Template kopiert und angepasst werden.
 */

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzberichte'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<!-- Filter- und Suchformular -->
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<!-- Tabelle mit allen Einsatzberichten -->
		<table class="table table-striped">
			<caption>BlaulichtMonitor Einsatzberichte</caption>
			<thead>
				<tr>
					<th>
						<!-- Sortierbarer Spaltenkopf: ID -->
						<?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
					<th>Status</th>
					<th>
						<!-- Sortierbarer Spaltenkopf: Alarmierungszeit -->
						<?php echo HTMLHelper::_('searchtools.sort', 'Alarmierungszeit', 'a.alarmierungszeit', $listDirn, $listOrder); ?>
					</th>
					<th>Einsatzart</th>
					<th>Einsatzort</th>
					<th>Kurzbericht</th>
					<th>Einheiten</th>
					<th>
						<!-- Sortierbarer Spaltenkopf: Zugriffe -->
						<?php echo HTMLHelper::_('searchtools.sort', 'Zugriffe', 'a.counter_clicks', $listDirn, $listOrder); ?>
					</th>
					<th>Erstellt am</th>
					<th>Bearbeitet am</th>
				</tr>
			</thead>
			<tbody>
				<!-- Zeilen für jeden Einsatzbericht -->
				<?php foreach ($this->items as $item) : ?>
					<tr>
						<td><?php echo $item->id; ?></td>
						<td>
							<!-- Status (veröffentlicht/nicht veröffentlicht) -->
							<?php echo HTMLHelper::_('jgrid.published', $item->veroeffentlicht, $i, 'einsatzberichte.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
						</td>
						<td>
							<?php $dt_alarmierungszeit = \DateTime::createFromFormat('Y-m-d H:i:s', $item->alarmierungszeit);
							echo $dt_alarmierungszeit ? $dt_alarmierungszeit->format('d.m.Y H:i:s') : htmlspecialchars($item->alarmierungszeit); ?>
						</td>
						<td><a href="<?php echo Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzbericht.edit&id=' . $item->id); ?>"><?php echo $item->einsatzart_title; ?></a></td>
						<td><?php echo $item->einsatzort_strasse; ?></td>
						<td><?php echo $item->einsatzkurzbericht; ?></td>
						<td>
							<div class="d-flex flex-wrap justify-content-between gap-1">
								<!-- Einheiten als Badges -->
								<?php $einheiten = explode(',', $item->einheiten_liste);
								foreach ($einheiten as $einheit) {
									$einheit = trim($einheit);
									if ($einheit) {
										echo '<span class="flex-fill badge bg-primary border">' . htmlspecialchars($einheit) . '</span>';
									}
								} ?>
							</div>
						</td>
						<td><span class="badge bg-success fs-5"><?php echo $item->counter_clicks; ?></span></td>
						<td>
							<?php $dt_created = \DateTime::createFromFormat('Y-m-d H:i:s', $item->created);
							echo $dt_created ? $dt_created->format('d.m.Y H:i:s') : htmlspecialchars($item->created); ?>
						</td>
						<td>
							<?php $dt_modified = \DateTime::createFromFormat('Y-m-d H:i:s', $item->modified);
							echo $dt_modified ? $dt_modified->format('d.m.Y H:i:s') : htmlspecialchars($item->modified); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<!-- Pagination -->
	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>