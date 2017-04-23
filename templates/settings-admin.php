<?php
?>
<div id="issuetemplate" class="section">
	<h2 class="inlineblock"><?php p($l->t('Issue reporting')); ?></h2>
	<p>
		<?php p($l->t("For reporting potential security issues please see")); ?> <a href="https://nextcloud.com/security/">https://nextcloud.com/security/</a>
	</p>
	<p><strong><?php p($l->t("Please always check if the automatically filled out information is correct and there is nothing important missing, before reporting the issue.")); ?></strong></p>


	<form method="GET" action="#">
		<p>
			<label for="repository"><?php p($l->t("Affected component")); ?></label>
			<select id="repository">
				<?php foreach ($_['repos'] as $id => $info): ?>
					<option value="<?php p($info['bugs']); ?>" <?php if($id === $_['app']) { p(' selected'); } ?> data-id="<?php p($id); ?>"><?php p($info['name']); ?></option>
				<?php endforeach; ?>
			</select>
			<?php p($_['app']); ?>
		</p>
		<p>
			<input type="text" id="issue-title" placeholder="<?php p($l->t("Issue title")); ?>" />
		</p>
		<textarea id="issue-description">### Steps to reproduce
1.
2.
3.

### Expected behaviour
Tell us what should happen

### Actual behaviour
Tell us what happens instead</textarea>
		<h3>Server information details <input type="button" value="<?php p($l->t('Show')); ?>" id="toggle-details"/></h3>
		<textarea id="issue-serverinfo"><?php p($_['issueTemplate']); ?></textarea>

		<p id="status-text"> </p>

		<input id="copyissue" type="button" value=" <?php p($l->t("Copy text to clipboard")); ?> " />
		<input class="primary" type="submit" value=" <?php p($l->t("File a new issue on GitHub")); ?> " id="submit-issue" />

	</form>

</div>


