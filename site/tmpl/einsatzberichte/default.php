<?php
\defined('_JEXEC');

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

HTMLHelper::_(
    'stylesheet',
    'com_blaulichtmonitor/einsatzberichte-override.css',
    ['version' => 'auto', 'relative' => true]
);

$wam = Factory::getApplication()->getDocument()->getWebAssetManager();
$wam->useStyle('com_blaulichtmonitor.einsatzberichte');
?>

<form>
    <div class="items-limit-box">
        <?php echo $this->pagination->getLimitBox(); ?>
    </div>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Beschreibung</th>
                    <th scope="col">Alarmierungszeit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <th scope="row"><?php echo $item->id; ?></th>
                        <td><a href="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzbericht&id=' . $item->id); ?>"><?php echo $item->beschreibung; ?></a></td>
                        <td><?php echo $item->alarmierungszeit; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div>
        <?php echo $this->pagination->getResultsCounter(); ?>
    </div>
    <?php echo $this->pagination->getListFooter(); ?>
    <input type="hidden" name="task" value="einsaetze">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>