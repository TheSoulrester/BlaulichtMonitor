<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzberichte'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row"> <!-- Aktuell verbuggt, nach Suche ist Sortierung ASC - Pagination funktioniert auch nicht -->
        <div class="col-md-12">
            <?php echo LayoutHelper::render(
                'joomla.searchtools.default',
                ['view' => $this]
            ); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <caption>BlaulichtMonitor Einsatzberichte</caption>
            <thead>
                <tr>
                    <th>
                        <?php echo HTMLHelper::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                    <th>Status</th>
                    <th>
            <?php echo HTMLHelper::_('searchtools.sort', 'Alarmierungszeit', 'alarmierungszeit', $listDirn, $listOrder); ?>
        </th>
                    <th>Einsatzart</th>
                    <th>Einsatzort</th>
                    <th>Kurzbericht</th>
                    <th>Zugriffe</th>
                    <th>Einheiten</th>
                    <th>Erstellt am</th>
                    <th>Bearbeitet am</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td><?php echo $item->id; ?></td>
                        <td><?php echo $item->veroeffentlicht; ?></td>
                        <td><?php echo $item->alarmierungszeit; ?></td>
                        <td><?php echo $item->einsatzart_title; ?></td>
                        <td><?php echo $item->einsatzort_strasse; ?></td>
                        <td><?php echo $item->einsatzkurzbericht; ?></td>
                        <td><?php echo $item->counter_clicks; ?></td>
                        <td>
                            <?php
                            $einheiten = explode(',', $item->einheiten_liste);
                    foreach ($einheiten as $einheit) {
                        $einheit = trim($einheit);
                        if ($einheit) {
                            echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($einheit) . '</span>';
                        }
                    }
                    ?>
                        </td>
                        <td><?php echo $item->created; ?></td>
                        <td><?php echo $item->modified; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->pagination->getListFooter(); ?>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
