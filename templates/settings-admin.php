<div id="issuetemplate" class="section">
	<h2 class="inlineblock"><?php p($l->t('Issue Template')); ?></h2>
	<p>Use this for reporting an issue on <a href="https://github.com/nextcloud/server/issues/new">GitHub</a> with your current server setup.</p>

	<p>Thanks for reporting issues back to Nextcloud! This is the issue tracker of Nextcloud, if you have any support question please check out https://nextcloud.com/support<br />
		This is the bug tracker for the Server component. Find other components at https://github.com/nextcloud/<br />
		For reporting potential security issues please see https://nextcloud.com/security/<br />
		To make it possible for us to help you please fill out below information carefully.<br />
	</p>
	<form method="GET" action="https://github.com/nextcloud/server/issues/new">
	<textarea name="body" width="100%" height="400" style="width: 100%; height:400px;"><?php p($_['issueTemplate']); ?></textarea>
	<input type="submit" value=" File a new issue on GitHub" />
	</form>
</div>

<script>

</script>