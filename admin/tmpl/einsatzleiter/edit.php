<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

// Backend-Behaviors
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Defensive Checks
if (empty($this->form)) {
    echo '<div class="alert alert-danger">Formular konnte nicht geladen werden.</div>';
    return;
}

// Item-ID sicher bestimmen (auch wenn $this->item null ist)
$itemId = (int) (!empty($this->item) && isset($this->item->id) ? $this->item->id : 0);

// Optional: konkretes Fieldset angeben, falls du in der XML <fieldset name="basic"> nutzt:
$fieldsetName = null; // z.B. 'basic' setzen, wenn vorhanden
$fields = $fieldsetName ? $this->form->getFieldset($fieldsetName) : $this->form->getFieldset();
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzleiter&layout=edit&id=' . $itemId); ?>"
      method="post"
      name="adminForm"
      id="einsatzleiter-form"
      class="form-validate">

    <div class="container">
        <div class="card mb-4">
            <div class="card-header fw-bold">Einsatzleiter</div>
            <div class="card-body">
                <div class="row">
                    <?php
                    // Sichtbare Felder ausgeben
                    foreach ($fields as $field) {
                        if ($field->hidden) {
                            continue;
                        }
                        echo '<div class="mb-3">';
                        echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
                        echo $field->input;
                        if (!empty($field->description)) {
                            echo '<small class="form-text text-muted">' . $field->description . '</small>';
                        }
                        echo '</div>';
                    }

                    // Versteckte Felder explizit rendern (damit id/created/... wirklich POSTen)
                    // Passe die Liste an deine XML an:
                    $hiddenNames = ['id','created','created_by','modified','modified_by','ordering'];
                    foreach ($hiddenNames as $name) {
                        if ($this->form->getField($name)) {
                            echo $this->form->getInput($name);
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
