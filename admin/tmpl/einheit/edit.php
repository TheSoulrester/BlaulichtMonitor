<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

// Initialisiere die Formularvalidierung und Keepalive-Verhalten für das Backend-Formular
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Feldgruppen
$fieldsBasis = ['title', 'name', 'url', 'organisation_id']; // beschreibung entfernt, organisation_id hinzugefügt
$fieldsStandort = ['standort_title', 'standort_strasse', 'standort_hausnummer', 'standort_plz', 'standort_ort'];
$fieldsOrganisation = ['organisation_id'];

?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einheit&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="einheit-form" class="form-validate">
	<div class="container">

		<!-- Basisdaten -->
		<div class="card mb-4">
			<div class="card-header fw-bold">Basisdaten</div>
			<div class="card-body">
				<div class="row">
					<?php foreach ($fieldsBasis as $fieldName) :
						$field = $this->form->getField($fieldName);
						if ($field) : ?>
							<div class="col-md-6 mb-3">
								<label class="form-label" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
								<?php echo $field->input; ?>
								<?php if ($field->description) : ?>
									<small class="form-text text-muted"><?php echo $field->description; ?></small>
								<?php endif; ?>
							</div>
					<?php endif;
					endforeach; ?>
				</div>
				<?php
				// beschreibung ans Ende der Karte platzieren
				$field = $this->form->getField('beschreibung');
				if ($field) : ?>
					<div class="mb-3">
						<label class="form-label" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
						<?php echo $field->input; ?>
						<?php if ($field->description) : ?>
							<small class="form-text text-muted"><?php echo $field->description; ?></small>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Standort -->
		<div class="card mb-4">
			<div class="card-header fw-bold">Standort</div>
			<div class="card-body">
				<!-- Standort Titel (volle Breite) -->
				<div class="row">
					<div class="col-12 mb-3">
						<?php
						$field = $this->form->getField('standort_title');
						if ($field) : ?>
							<label class="form-label fw-bold" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
							<?php echo $field->input; ?>
							<?php if ($field->description) : ?>
								<small class="form-text text-muted"><?php echo $field->description; ?></small>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<!-- Straße und Hausnummer nebeneinander -->
				<div class="row">
					<div class="col-md-8 mb-3">
						<?php
						$field = $this->form->getField('standort_strasse');
						if ($field) : ?>
							<label class="form-label" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
							<?php echo $field->input; ?>
						<?php endif; ?>
					</div>
					<div class="col-md-4 mb-3">
						<?php
						$field = $this->form->getField('standort_hausnummer');
						if ($field) : ?>
							<label class="form-label" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
							<?php echo $field->input; ?>
						<?php endif; ?>
					</div>
				</div>
				<!-- PLZ und Ort nebeneinander -->
				<div class="row">
					<div class="col-md-4 mb-3">
						<?php
						$field = $this->form->getField('standort_plz');
						if ($field) : ?>
							<label class="form-label" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
							<?php echo $field->input; ?>
						<?php endif; ?>
					</div>
					<div class="col-md-8 mb-3">
						<?php
						$field = $this->form->getField('standort_ort');
						if ($field) : ?>
							<label class="form-label" for="<?php echo $field->id; ?>"><?php echo $field->label; ?></label>
							<?php echo $field->input; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
