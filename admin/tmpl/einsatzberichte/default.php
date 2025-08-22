<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Template für die Einsatzberichte-Übersicht im Backend.
 */

// WebAssets laden
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns')
	->useScript('multiselect')
	->useScript('bootstrap.modal')
	->useScript('bootstrap.dropdown');

// Benutzer/Sortierung
$user      = Factory::getApplication()->getIdentity();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Site-Root (nicht /administrator/)
$siteRoot = rtrim(Uri::root(), '/') . '/../';

// Bilder der aktuell angezeigten Einsatzberichte laden
$bilderByReport = [];
if (!empty($this->items)) {
	$db  = Factory::getContainer()->get('DatabaseDriver');
	$ids = array_map(static fn($it) => (int) $it->id, $this->items);
	$ids = array_values(array_unique(array_filter($ids)));

	if (!empty($ids)) {
		$query = $db->getQuery(true)
			->select($db->qn(['id', 'einsatzbericht_id', 'filename', 'thumbnail']))
			->from($db->qn('#__blaulichtmonitor_einsatzbilder'))
			->where('einsatzbericht_id IN (' . implode(',', $ids) . ')')
			->order($db->qn('id') . ' ASC');

		$db->setQuery($query);
		$rows = (array) $db->loadAssocList();

		foreach ($rows as $r) {
			$rid = (int) ($r['einsatzbericht_id'] ?? 0);
			if ($rid > 0) {
				$bilderByReport[$rid][] = $r;
			}
		}
	}
}
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzberichte'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<h1 hidden class="page-title">Einsatzberichte</h1>

				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<div class="table-responsive">
						<table class="table table-striped itemList" id="einsatzberichteList">
							<caption class="visually-hidden">
								BlaulichtMonitor Einsatzberichte
								<span id="orderedBy">Sortiert nach </span>
								<span id="filteredBy">Gefiltert nach </span>
							</caption>
							<thead>
								<tr>
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
									</th>
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
									</th>
									<th scope="col" class="text-center">Veröffentlicht</th>
									<th scope="col" class="text-center">Bilder</th>
									<th scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'Alarmierungszeit', 'a.alarmierungszeit', $listDirn, $listOrder); ?>
									</th>
									<th scope="col">Einsatzart</th>
									<th scope="col">Einsatzort</th>
									<th scope="col">Kurzbericht</th>
									<th scope="col" class="text-center">Einheiten</th>
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', 'Zugriffe', 'a.counter_clicks', $listDirn, $listOrder); ?>
									</th>
									<th scope="col">Erstellt</th>
									<th scope="col">Bearbeitet</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($this->items as $i => $item) : ?>
									<?php $canChange = $user->authorise('core.edit.state', 'com_blaulichtmonitor'); ?>
									<tr>
										<td class="text-center">
											<?php echo HTMLHelper::_('grid.id', $i, (int) $item->id, false, 'cid'); ?>
										</td>
										<td class="text-center">
											<?php echo '<span class="badge bg-primary border">#' . (int) $item->id . '</span>'; ?>
										</td>
										<td class="text-center">
											<?php echo HTMLHelper::_('jgrid.published', (int) $item->published, $i, 'einsatzberichte.', $canChange, 'cb'); ?>
										</td>
										<td class="text-center">
											<?php
											$imgs    = $bilderByReport[(int) $item->id] ?? [];
											$count   = count($imgs);
											$modalId = 'bilderModal-' . (int) $item->id;
											?>
											<button type="button"
												class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center gap-2 text-nowrap"
												data-bs-toggle="modal"
												data-bs-target="#<?php echo $modalId; ?>"
												<?php echo $count === 0 ? 'disabled' : ''; ?>>
												<span>Bilder</span>
												<?php if ($count > 0): ?>
													<span class="badge text-bg-light position-static"><?php echo (int) $count; ?></span>
												<?php endif; ?>
											</button>

											<?php
											$body = '';
											if ($count === 0) {
												$body = '<p class="text-muted mb-0">Keine Bilder vorhanden.</p>';
											} else {
												$body .= '<div class="container-fluid">';
												$body .= '<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">';

												foreach ($imgs as $img) {
													$imgId    = (int) ($img['id'] ?? 0);
													$thumbRel = $img['thumbnail'] ?: $img['filename'];
													$thumbUrl = $siteRoot . ltrim((string) $thumbRel, '/');
													$fullUrl  = $siteRoot . ltrim((string) $img['filename'], '/');

													$editUrl    = Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzbild.edit&id=' . $imgId);
													$deleteAction = Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzbild.delete');

													$body .= '<div class="col">';
													$body .= '  <div class="position-relative bg-body border rounded overflow-hidden">';
													$body .= '    <div class="ratio ratio-4x3">';
													$body .= '      <img src="' . htmlspecialchars($thumbUrl, ENT_QUOTES) . '" alt="Bild #' . $imgId . '" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">';
													$body .= '    </div>';

													// Dezentes Aktionsmenü oben rechts (Dropdown)
													$body .= '    <div class="position-absolute top-0 end-0 m-2 z-3">';
													$body .= '      <div class="dropdown">';
													$body .= '        <button class="btn btn-primary btn-sm p-1 lh-1 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Aktionen">';
													$body .= '          <span class="icon-cog" aria-hidden="true"></span>';
													$body .= '        </button>';
													$body .= '        <ul class="dropdown-menu dropdown-menu-end">';
													$body .= '          <li>';
													$body .= '				<a class="dropdown-item d-flex align-items-center gap-2" href="' . htmlspecialchars($editUrl, ENT_QUOTES) . '">';
													$body .= '					<span class="icon-edit" aria-hidden="true"></span>';
													$body .= '					<span>Bearbeiten</span>';
													$body .= '				</a>';
													$body .= '			</li>';
													$body .= '          <li>';
													$body .= '            <form method="post" action="' . htmlspecialchars($deleteAction, ENT_QUOTES) . '" onsubmit="return confirm(\'Bild wirklich löschen?\')">';
													$body .= '              <input type="hidden" name="id" value="' . $imgId . '">';
													$body .=                    HTMLHelper::_('form.token');
													$body .= '              <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">';
													$body .= '					<span class="icon-trash" aria-hidden="true"></span>';
													$body .= '					<span>Löschen</span>';
													$body .= '				</button>';
													$body .= '            </form>';
													$body .= '          </li>';
													$body .= '        </ul>';
													$body .= '      </div>';
													$body .= '    </div>';

													// Klick aufs Bild: Original öffnen
													$body .= '    <a href="' . htmlspecialchars($fullUrl, ENT_QUOTES) . '" target="_blank" rel="noopener" class="stretched-link" aria-label="Originalbild öffnen"></a>';
													$body .= '  </div>';
													$body .= '</div>';
												}

												$body .= '</div>'; // row
												$body .= '</div>'; // container
											}

											echo HTMLHelper::_(
												'bootstrap.renderModal',
												$modalId,
												[
													'title' => 'Bilder zu Einsatz #' . (int) $item->id,
													'modal-dialog-scrollable' => true,
													'modal-dialog-centered'   => true,
													'modal-dialog-class'      => 'modal-xl modal-fullscreen-sm-down',
													'backdrop' => true,
													'keyboard' => true,
													'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>',
												],
												$body
											);
											?>
										</td>
										<td>
											<?php
											$dt_alarmierungszeit = \DateTime::createFromFormat('Y-m-d H:i:s', (string) $item->alarmierungszeit);
											if ($dt_alarmierungszeit) {
												echo $dt_alarmierungszeit->format('d.m.Y') . '<br>';
												echo $dt_alarmierungszeit->format('H:i') . ' Uhr';
											} else {
												echo htmlspecialchars((string) $item->alarmierungszeit);
											}
											?>
										</td>
										<td>
											<a href="<?php echo Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzbericht.edit&id=' . (int) $item->id); ?>">
												<?php echo htmlspecialchars((string) $item->einsatzart_title); ?>
											</a>
										</td>
										<td>
											<?php
											$strasse    = (string) ($item->einsatzort_strasse ?? '');
											$hausnummer = (string) ($item->einsatzort_hausnummer ?? '');
											$plz        = (string) ($item->einsatzort_plz ?? '');
											$stadt      = (string) ($item->einsatzort_stadt ?? '');

											$adresse = $strasse;
											if ($hausnummer !== '') {
												$adresse .= ' ' . $hausnummer;
											}
											echo htmlspecialchars($adresse);

											if ($plz !== '' || $stadt !== '') {
												echo '<br>';
												if ($plz !== '') {
													echo htmlspecialchars($plz);
												}
												if ($stadt !== '') {
													echo ' ' . htmlspecialchars($stadt);
												}
											}
											?>
										</td>
										<td><?php echo htmlspecialchars((string) $item->einsatzkurzbericht); ?></td>
										<td class="text-center">
											<div class="d-flex flex-wrap justify-content-between gap-1">
												<?php
												$einheiten = explode(',', (string) ($item->einheiten_liste ?? ''));
												foreach ($einheiten as $einheit) {
													$einheit = trim($einheit);
													if ($einheit !== '') {
														echo '<span class="flex-fill badge bg-primary border">' . htmlspecialchars($einheit) . '</span>';
													}
												}
												?>
											</div>
										</td>
										<td class="text-center">
											<span class="badge bg-success fs-5"><?php echo (int) $item->counter_clicks; ?></span>
										</td>
										<td>
											<div class="d-flex flex-column">
												<?php
												$dt_created = !empty($item->created) ? \DateTime::createFromFormat('Y-m-d H:i:s', (string) $item->created) : false;
												if ($dt_created) {
													echo '<span>' . $dt_created->format('d.m.Y') . '</span>';
													echo '<span>' . $dt_created->format('H:i') . ' Uhr</span>';
												} elseif (!empty($item->created)) {
													echo '<span>' . htmlspecialchars((string) $item->created) . '</span>';
												} else {
													echo '<span>-</span>';
												}
												?>
												<?php if (!empty($item->created_by_name)) : ?>
													<small class="text-muted text-truncate" style="max-width: 120px;">
														<?php echo htmlspecialchars((string) $item->created_by_name); ?>
													</small>
												<?php endif; ?>
											</div>
										</td>
										<td>
											<div class="d-flex flex-column">
												<?php
												$dt_modified = !empty($item->modified) ? \DateTime::createFromFormat('Y-m-d H:i:s', (string) $item->modified) : false;
												if ($dt_modified) {
													echo '<span>' . $dt_modified->format('d.m.Y') . '</span>';
													echo '<span>' . $dt_modified->format('H:i') . ' Uhr</span>';
												} elseif (!empty($item->modified)) {
													echo '<span>' . htmlspecialchars((string) $item->modified) . '</span>';
												} else {
													echo '<span>-</span>';
												}
												?>
												<?php if (!empty($item->modified_by_name)) : ?>
													<small class="text-muted text-truncate" style="max-width: 120px;">
														<?php echo htmlspecialchars((string) $item->modified_by_name); ?>
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

	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
