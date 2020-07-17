<div class="wrap">
	<h1><?php _e('Skip Navigation Links Setting') ?></h1>

	<form method="post" action="">
		<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<tr>
					<th scope="col"><?php _e('navigation link text') ?></th>
					<th scope="col"><?php _e('target id') ?></th>
					<th scope="col"><?php _e('delete') ?></th>
				</tr>
			</thead>

			<tbody id="the-list">
				<?php if(!empty($rows)): ?>
				<?php foreach ($rows as $row): ?>
					<tr>
						<td>
							<input type="text" name="links[<?= $row->id ?>][label]" value="<?= $row->label ?>">
						</td>
						<td>
							<input type="text" name="links[<?= $row->id ?>][target_id]" value="<?= $row->target_id ?>">
						</td>
						<td>
							<a href="options-general.php?page=skip-navigation-links&action=delete-row&id=<?= $row->id ?>" class="delete"><?php _e('delete') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				<tr id="add-new-block">
					<td colspan="3" style="padding-left: 2rem;">
						<button id="add-new-block-button" type="button" class="button button-secondary">+ <?php _e('add row') ?></button>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('update') ?>">
		</p>
	</form>
</div>

<script type="text/javascript">
	jQuery(function () {
		var i = 1;
		addRow(i);

		jQuery('#add-new-block-button').on('click', function () {
			i ++;
			addRow(i);
		});
	});

	function addRow(i) {
		jQuery('<tr class="form-block"><td><input name="newLinks[' + i + '][label]" type="text"></td><td><input name="newLinks[' + i + '][target_id]" type="text"></td><td></td></tr>')
				.insertBefore('#add-new-block');
	}
</script>
