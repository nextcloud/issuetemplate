/**
 * Nextcloud - issuetemplate
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julius Härtl <jus@bitgrid.net>
 * @copyright Julius Härtl 2016
 */

function getIssueText() {
	var body = "";
	body += $('#issue-description').val();
	body += "\n\n";
	body += $('#issue-serverinfo').val();
	return body;
}

(function ($, OC) {

	$(document).ready(function () {

		$('#submit-issue').click(function (e) {
			e.preventDefault();
			var body = getIssueText();
			window.open("https://github.com/nextcloud/server/issues/new?body=" + encodeURIComponent(body));
		});

		var copybutton = document.getElementById('copyissue');
		var clipboard = new Clipboard(copybutton, {
			text: function (trigger) {
				return getIssueText();
			}
		});

		$('textarea').bind('input propertychange', function() {
			var body = getIssueText();
			if(body.length>3190) {
				$('#submit-issue').hide();
				$('#copyissue').addClass('primary');
				$('#status-text').html(t('Issue to long to send over to GitHub. Please copy the text and paste it here:') + ' <a href="https://github.com/nextcloud/server/issues/new">https://github.com/nextcloud/server/issues/new</a>');
			} else {
				$('#submit-issue').show();
				$('#copyissue').removeClass('primary');
				$('#status-text').text('');
			}
		});

	});

})(jQuery, OC);