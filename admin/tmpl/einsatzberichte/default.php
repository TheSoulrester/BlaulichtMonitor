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

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns'); // Implementierung eines Buttons zum Anzeigen/Ausblenden von Spalten

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzberichte'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<!-- Filter- und Suchformular -->
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
				<h1 hidden class="page-title">Einsatzberichte</h1>
				<div class="table-responsive">
					<table class="table table-striped itemList" id="articleList">
						<caption>BlaulichtMonitor Einsatzberichte</caption>
						<caption class="visually-hidden">BlaulichtMonitor Einsatzberichte
							<span id="orderedBy">Sortiert nach </span>
							<span id="filteredBy">Gefiltert nach </span>
						</caption>
						<thead>
							<tr>
								<th scope="col" class="text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="text-center">Status</th>
								<th scope="col" class="">
									<?php echo HTMLHelper::_('searchtools.sort', 'Alarmierungszeit', 'a.alarmierungszeit', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="">Einsatzart</th>
								<th scope="col" class="">Einsatzort</th>
								<th scope="col" class="">Kurzbericht</th>
								<th scope="col" class="text-center">Einheiten</th>
								<th scope="col" class="text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'Zugriffe', 'a.counter_clicks', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none">Erstellt</th>
								<th scope="col" class="">Bearbeitet</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->items as $item) : ?>
								<tr>
									<td class="text-center"><?php echo '<span class="badge bg-primary border">#' . $item->id . '</span>'; ?></td>
									<td class="text-center">
										<?php echo HTMLHelper::_('jgrid.published', $item->veroeffentlicht, $i, 'einsatzberichte.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
									</td>
									<td>
										<?php
										$dt_alarmierungszeit = \DateTime::createFromFormat('Y-m-d H:i:s', $item->alarmierungszeit);
										if ($dt_alarmierungszeit) {
											echo $dt_alarmierungszeit->format('d.m.Y') . '<br>';
											echo $dt_alarmierungszeit->format('H:i') . ' Uhr';
										} else {
											echo htmlspecialchars($item->alarmierungszeit);
										}
										?>
									</td>
									<td><a href="<?php echo Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzbericht.edit&id=' . $item->id); ?>"><?php echo $item->einsatzart_title; ?></a></td>
									<td><?php echo $item->einsatzort_strasse; ?></td>
									<td><?php echo $item->einsatzkurzbericht; ?></td>
									<td class="text-center">
										<div class="d-flex flex-wrap justify-content-between gap-1">
											<?php $einheiten = explode(',', $item->einheiten_liste);
											foreach ($einheiten as $einheit) {
												$einheit = trim($einheit);
												if ($einheit) {
													echo '<span class="flex-fill badge bg-primary border">' . htmlspecialchars($einheit) . '</span>';
												}
											} ?>
										</div>
									</td>
									<td class="text-center"><span class="badge bg-success fs-5"><?php echo $item->counter_clicks; ?></span></td>
									<td>
										<div class="d-flex flex-column">
											<?php
											$dt_created = \DateTime::createFromFormat('Y-m-d H:i:s', $item->created);
											if ($dt_created) {
												echo '<span>' . $dt_created->format('d.m.Y') . '</span>';
												echo '<span>' . $dt_created->format('H:i') . ' Uhr</span>';
											} else {
												echo '<span>' . htmlspecialchars($item->created) . '</span>';
											}
											?>
											<?php if (!empty($item->created_by_name)) : ?>
												<small class="text-muted text-truncate" style="max-width: 120px;">
													<?php echo htmlspecialchars($item->created_by_name); ?>
												</small>
											<?php endif; ?>
										</div>
									</td>
									<td>
										<div class="d-flex flex-column">
											<?php
											$dt_modified = \DateTime::createFromFormat('Y-m-d H:i:s', $item->modified);
											if ($dt_modified) {
												echo '<span>' . $dt_modified->format('d.m.Y') . '</span>';
												echo '<span>' . $dt_modified->format('H:i') . ' Uhr</span>';
											} else {
												echo '<span>' . htmlspecialchars($item->modified) . '</span>';
											}
											?>
											<?php if (!empty($item->modified_by_name)) : ?>
												<small class="text-muted text-truncate" style="max-width: 120px;">
													<?php echo htmlspecialchars($item->modified_by_name); ?>
												</small>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- Pagination -->
	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
