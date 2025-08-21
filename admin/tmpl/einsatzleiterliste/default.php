<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Template für die Einsatzleiter-Übersicht im Backend.
 * Stellt das HTML für die Listenansicht bereit, inklusive Filterformular, Tabelle und Pagination.
 */

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns')
	->useScript('multiselect');

$user      = Factory::getApplication()->getIdentity();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$canOrder  = $user->authorise('core.edit.state', 'com_blaulichtmonitor');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_blaulichtmonitor&task=einsatzleiterliste.saveOrderAjax&tmpl=component';
	HTMLHelper::_('draggablelist.draggable', 'einsatzleiterlisteList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzleiterliste'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">

				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<h1 hidden class="page-title">Einsatzleiter</h1>

				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<div class="table-responsive">
						<table class="table table-striped itemList" id="einsatzleiterlisteList">
							<caption class="visually-hidden">
								BlaulichtMonitor Einsatzleiter
								<span id="orderedBy">Sortiert nach </span>
								<span id="filteredBy">Gefiltert nach </span>
							</caption>
							<thead>
								<tr>
									<th width="1%" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'icon-menu'); ?>
									</th>
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
									</th>
									<th scope="col" class="text-center">
										<?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
									</th>
									<th scope="col" class="">
										<?php echo HTMLHelper::_('searchtools.sort', 'Name', 'a.name', $listDirn, $listOrder); ?>
									</th>
									<th scope="col" class="">Erstellt</th>
									<th scope="col" class="">Bearbeitet</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($this->items as $i => $item) : ?>
									<tr>
										<td class="order text-center">
											<?php
											$iconClass = (!$canOrder || !$saveOrder) ? 'inactive tip-top' : '';
											?>
											<span class="sortable-handler <?php echo $iconClass; ?>">
												<span class="icon-menu" aria-hidden="true"></span>
											</span>
											<input type="hidden" name="order[]" value="<?php echo $item->ordering; ?>" />
										</td>
										<td class="text-center">
											<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid'); ?>
										</td>
										<td class="text-center">
											<span class="badge bg-primary border">#<?php echo $item->id; ?></span>
										</td>
										<td>
											<a href="<?php echo Route::_('/administrator/index.php?option=com_blaulichtmonitor&task=einsatzleiter.edit&id=' . $item->id); ?>">
												<?php echo $item->name; ?>
											</a>
										</td>
										<td>
											<div class="d-flex flex-column">
												<?php
												$dt_created = !empty($item->created) ? \DateTime::createFromFormat('Y-m-d H:i:s', $item->created) : false;
												if ($dt_created) {
													echo '<span>' . $dt_created->format('d.m.Y') . '</span>';
													echo '<span>' . $dt_created->format('H:i') . ' Uhr</span>';
												} elseif (!empty($item->created)) {
													echo '<span>' . htmlspecialchars($item->created) . '</span>';
												} else {
													echo '<span>-</span>';
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
												$dt_modified = !empty($item->modified) ? \DateTime::createFromFormat('Y-m-d H:i:s', $item->modified) : false;
												if ($dt_modified) {
													echo '<span>' . $dt_modified->format('d.m.Y') . '</span>';
													echo '<span>' . $dt_modified->format('H:i') . ' Uhr</span>';
												} elseif (!empty($item->modified)) {
													echo '<span>' . htmlspecialchars($item->modified) . '</span>';
												} else {
													echo '<span>-</span>';
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

	<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
