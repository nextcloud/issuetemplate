/**
 * Nextcloud - issuetemplate
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julius Härtl <jus@bitgrid.net>
 * @copyright Julius Härtl 2016
 */
function checkLength() {
	var body = getIssueText();
	if(body.length>4096) {
		$('#submit-issue').hide();
		$('#copyissue').addClass('primary');
		$('#status-text').html(t('issuetemplate','Issue to long to send over to GitHub. Please copy the text and paste it here:') + ' <a href="https://github.com/nextcloud/server/issues/new">https://github.com/nextcloud/server/issues/new</a>');
	} else {
		$('#submit-issue').show();
		$('#copyissue').removeClass('primary');
		$('#status-text').text('GitHub might show an error page if you are not logged in.');
	}
}
function getIssueText() {
	var body = "";
	body += $('#issue-description').val();
	body += "\n\n";
	body += $('#issue-serverinfo').val();
	return body;
}

(function ($, OC) {

	$(document).ready(function () {

		checkLength();

		$('#toggle-details').click(function (e) {
			$('#issue-serverinfo').slideToggle(function() {
				if($(this).is(":hidden")) {
					$('#toggle-details').val("Show");
				} else {
					$('#toggle-details').val("Hide");
				}
			});

		});

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
			checkLength();
		});

	});

})(jQuery, OC);