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
				<?php foreach ($_['repos'] as $name => $url): ?>
					<option value="<?php p($url); ?>"><?php p($name); ?></option>
				<?php endforeach; ?>
			</select>
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

		<div class="wizard-buttons">
			<input id="copyissue" type="button" class="<?php if ($_['config-github-token'] === true) { ?> hidden<?php } ?>" value=" <?php p($l->t("Copy text to clipboard")); ?> " />
			<input id="submit-github-http" type="button" class="primary <?php if ($_['config-github-token'] === true) { ?> hidden<?php } ?>" type="submit" value=" <?php p($l->t("Create a new issue")); ?> " id="submit-issue" />
			<input id="submit-github-api" type="button" class="primary <?php if ($_['config-github-token'] === false) { ?> hidden<?php } ?>" value=" <?php p($l->t("Create a new issue")); ?> "/>
		</div>
	</form>

	<h2 class="github-settings">
		<?php p($l->t('Setup with GitHub')); ?>
		<span class="icon icon-checkmark-color<?php if ($_['config-github-token'] === false) { ?> hidden<?php } ?>"></span>
		<span class="icon icon-loading-small hidden"></span>
		<span class="icon icon-error-color hidden"></span>

	</h2>
	<div class="github-settings<?php if ($_['config-github-token'] === true) { ?> hidden<?php } ?>">
		<p><?php print_unescaped($l->t('In order to automatically create issues on GitHub, you need to create a <a href="https://github.com/settings/tokens" rel="nofollow">personal access token</a> and add it to the issue reporting app:')); ?></p>
		<p><label>Access token</label><input type="text" id="issuetemplate-access-token" placeholder="GitHub personal access token" /><button id="issuetemplate-access-token-save">Save</button></p>
	</div>

</div>


