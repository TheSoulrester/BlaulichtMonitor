<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Template für die Einsatzarten-Übersicht im Backend.
 * Stellt das HTML für die Listenansicht bereit, inklusive Filterformular, Tabelle und Pagination.
 * Nutzt Daten aus dem View (HtmlView).
 * Für weitere Views kann dieses Template kopiert und angepasst werden.
 */

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa WebAssetManager für die Einbindung von Scripts und Styles */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns') // Script für das Anzeigen/Ausblenden von Tabellenspalten
	->useScript('multiselect'); // Script für Mehrfachauswahl in der Tabelle

// Hole aktuellen Benutzer und Sortierparameter aus dem View-State
$user      = Factory::getApplication()->getIdentity();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$canOrder = $user->authorise('core.edit.state', 'com_blaulichtmonitor');
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_blaulichtmonitor&task=einsatzarten.saveOrderAjax&tmpl=component';
	HTMLHelper::_('draggablelist.draggable', 'einsatzartenList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzarten'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<!-- Such- und Filterformular für die Einsatzarten-Liste -->
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<!-- Überschrift für Screenreader, im UI ausgeblendet -->
				<h1 hidden class="page-title">Einsatzarten</h1>

				<?php if (empty($this->items)) : ?>
					<!-- Hinweis, falls keine Einsatzarten gefunden wurden -->
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<!-- Tabelle mit allen Einsatzarten -->
					<div class="table-responsive">
						<table class="table table-striped itemList" id="einsatzartenList">
							<caption class="visually-hidden">
								BlaulichtMonitor Einsatzarten
								<span id="orderedBy">Sortiert nach </span>
								<span id="filteredBy">Gefiltert nach </span>
							</caption>
							<thead>
								<tr>
									<th width="1%" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'icon-menu'); ?>
									</th>
									<!-- Checkbox für Mehrfachauswahl -->
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
									</th>
									<!-- Sortierbare Spalte: ID -->
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
									</th>
									<!-- Icon-Spalte -->
									<th scope="col" class="text-center">
										<?php echo Text::_('Icon'); ?>
									</th>
									<!-- Sortierbare Spalte: Title -->
									<th scope="col" class="">
										<?php echo HTMLHelper::_('searchtools.sort', 'Title', 'a.title', $listDirn, $listOrder); ?>
									</th>
									<!-- Erstellungsdatum -->
									<th scope="col" class="">Erstellt</th>
									<!-- Bearbeitungsdatum -->
									<th scope="col" class="">Bearbeitet</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($this->items as $i => $item) : ?>
									<?php $canChange = $user->authorise('core.edit.state', 'com_blaulichtmonitor'); ?>
									<tr>
										<td class="order text-center">
											<?php
											$iconClass = '';
											if (!$canOrder || !$saveOrder) {
												$iconClass = 'inactive tip-top';
											}
											?>
											<span class="sortable-handler <?php echo $iconClass; ?>">
												<span class="icon-menu" aria-hidden="true"></span>
											</span>
											<input type="hidden" name="order[]" value="<?php echo $item->ordering; ?>" />
										</td>
										<!-- Checkbox für die Auswahl einzelner Berichte -->
										<td class="text-center">
											<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid'); ?>
										</td>
										<!-- Anzeige der Bericht-ID als Badge -->
										<td class="text-center">
											<?php echo '<span class="badge bg-primary border">#' . $item->id . '</span>'; ?>
										</td>
										<!-- Icon anzeigen -->
										<td class="text-center">
											<?php
											$imgSrc = '';
											if (!empty($item->icon_url)) {
												// Absoluten Pfad erzeugen
												$imgSrc = Uri::root() . ltrim($item->icon_url, '/');
												// Pfad auf dem Server prüfen
												$serverPath = JPATH_ROOT . '/' . ltrim($item->icon_url, '/');
												if (is_file($serverPath)) {
													// Bild existiert
													echo '<img src="' . htmlspecialchars($imgSrc) . '" alt="' . htmlspecialchars($item->title) . '" style="max-width:32px;max-height:32px;object-fit:contain;border-radius:4px;" loading="lazy" />';
												} else {
													// Bild fehlt
													echo '<span class="text-danger" title="Bilddatei nicht gefunden"><span class="icon-warning"></span> fehlt</span>';
												}
											} else {
												echo '<span class="text-muted">–</span>';
											}
											?>
										</td>
										<!-- Title mit Link zur Bearbeitung -->
										<td>
											<a href="<?php echo Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzart.edit&id=' . $item->id); ?>">
												<?php echo $item->title; ?>
											</a>
										</td>
										<!-- Erstellungsdatum und Ersteller (ausgeblendet) -->
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
										<!-- Bearbeitungsdatum und Bearbeiter -->
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
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	// Pagination-Element für die Navigation zwischen Seiten
	echo $this->pagination->getListFooter();
	?>

	<!-- Versteckte Felder für die Formularverarbeitung -->
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); // CSRF-Schutz
	?>
</form>
