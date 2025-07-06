<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('form.token');

$link = Route::_('index.php?option=com_blaulichtmonitor&task=display.migrate&' . Session::getFormToken() . '=1');
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
            <div class="d-flex gap-2 mt-3">
                <form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.migrate'); ?>" method="post" class="mt-3">
                    <?= HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-warning"
                        onclick="return confirm('<?= Text::_('COM_BLAULICHTMONITOR_MIGRATION_CONFIRM'); ?>');">
                        <?= Text::_('COM_BLAULICHTMONITOR_MIGRATION_START_BUTTON'); ?>
                    </button>
                </form>
                <form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.cleantables'); ?>" method="post" class="mt-3">
                    <?= HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('<?= Text::_('COM_BLAULICHTMONITOR_CLEAN_CONFIRM'); ?>');">
                        <?= Text::_('COM_BLAULICHTMONITOR_CLEAN_BUTTON'); ?>
                    </button>
                </form>
                <form action="<?= Route::_('index.php?option=com_blaulichtmonitor&task=display.droptables'); ?>" method="post" class="mt-3">
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