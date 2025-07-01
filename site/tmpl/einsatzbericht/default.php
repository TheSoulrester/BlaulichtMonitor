<?php
defined('_JEXEC');
?>
<div class="einsatzbericht-item p-4">
    <h1><?php echo $this->item->beschreibung; ?></h1>
    <div id="alarmierungszeit" class="date">
        <?php echo $this->item->alarmierungszeit; ?>
    </div>
    <div id="created" class="date meta">
        <?php echo $this->item->created; ?>
    </div>
</div>