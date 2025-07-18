<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Template für die Einsatzberichte-Übersicht im Backend.
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
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzberichte'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<!-- Such- und Filterformular für die Einsatzberichte-Liste -->
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<!-- Überschrift für Screenreader, im UI ausgeblendet -->
				<h1 hidden class="page-title">Einsatzberichte</h1>

				<?php if (empty($this->items)) : ?>
					<!-- Hinweis, falls keine Einsatzberichte gefunden wurden -->
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<!-- Tabelle mit allen Einsatzberichten -->
					<div class="table-responsive">
						<table class="table table-striped itemList" id="einsatzberichteList">
							<caption class="visually-hidden">
								BlaulichtMonitor Einsatzberichte
								<span id="orderedBy">Sortiert nach </span>
								<span id="filteredBy">Gefiltert nach </span>
							</caption>
							<thead>
								<tr>
									<!-- Checkbox für Mehrfachauswahl -->
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
									</th>
									<!-- Sortierbare Spalte: ID -->
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
									</th>
									<!-- Status (veröffentlicht/entwurf) -->
									<th scope="col" class="text-center">Status</th>
									<!-- Sortierbare Spalte: Alarmierungszeit -->
									<th scope="col" class="">
										<?php echo HTMLHelper::_('searchtools.sort', 'Alarmierungszeit', 'a.alarmierungszeit', $listDirn, $listOrder); ?>
									</th>
									<!-- Einsatzart -->
									<th scope="col" class="">Einsatzart</th>
									<!-- Einsatzort -->
									<th scope="col" class="">Einsatzort</th>
									<!-- Kurzbericht -->
									<th scope="col" class="">Kurzbericht</th>
									<!-- Einheiten -->
									<th scope="col" class="text-center">Einheiten</th>
									<!-- Sortierbare Spalte: Zugriffe -->
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', 'Zugriffe', 'a.counter_clicks', $listDirn, $listOrder); ?>
									</th>
									<!-- Erstellungsdatum (ausgeblendet) -->
									<th scope="col" class="d-none">Erstellt</th>
									<!-- Bearbeitungsdatum -->
									<th scope="col" class="">Bearbeitet</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($this->items as $i => $item) : ?>
									<?php $canChange = $user->authorise('core.edit.state', 'com_blaulichtmonitor'); ?>
									<tr>
										<!-- Checkbox für die Auswahl einzelner Berichte -->
										<td class="text-center">
											<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid'); ?>
										</td>
										<!-- Anzeige der Bericht-ID als Badge -->
										<td class="text-center">
											<?php echo '<span class="badge bg-primary border">#' . $item->id . '</span>'; ?>
										</td>
										<!-- Status-Button (veröffentlicht/entwurf) -->
										<td class="text-center">
											<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'einsatzberichte.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
										</td>
										<!-- Alarmierungszeit formatiert -->
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
										<!-- Einsatzart mit Link zur Bearbeitung -->
										<td>
											<a href="<?php echo Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzbericht.edit&id=' . $item->id); ?>">
												<?php echo $item->einsatzart_title; ?>
											</a>
										</td>
										<!-- Einsatzort (Straße, Hausnummer, PLZ, Stadt) -->
										<td>
											<?php
											$strasse    = $item->einsatzort_strasse ?? '';
											$hausnummer = $item->einsatzort_hausnummer ?? '';
											$plz        = $item->einsatzort_plz ?? '';
											$stadt      = $item->einsatzort_stadt ?? '';

											$adresse = $strasse;
											if ($hausnummer !== '' && $hausnummer !== null) {
												$adresse .= ' ' . $hausnummer;
											}
											echo htmlspecialchars($adresse);

											if (($plz !== '' && $plz !== null) || ($stadt !== '' && $stadt !== null)) {
												echo '<br>';
												if ($plz !== '' && $plz !== null) {
													echo htmlspecialchars($plz);
												}
												if ($stadt !== '' && $stadt !== null) {
													echo ' ' . htmlspecialchars($stadt);
												}
											}
											?>
										</td>
										<!-- Kurzbericht zum Einsatz -->
										<td><?php echo $item->einsatzkurzbericht; ?></td>
										<!-- Einheiten als Badges -->
										<td class="text-center">
											<div class="d-flex flex-wrap justify-content-between gap-1">
												<?php
												$einheiten = explode(',', $item->einheiten_liste);
												foreach ($einheiten as $einheit) {
													$einheit = trim($einheit);
													if ($einheit) {
														echo '<span class="flex-fill badge bg-primary border">' . htmlspecialchars($einheit) . '</span>';
													}
												}
												?>
											</div>
										</td>
										<!-- Zugriffsanzahl als Badge -->
										<td class="text-center">
											<span class="badge bg-success fs-5"><?php echo $item->counter_clicks; ?></span>
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