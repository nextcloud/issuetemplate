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
		var url = $('#repository').val();
		$('#status-text').html(t('issuetemplate','Issue to long to send over to GitHub. Please copy the text and paste it here:') + ' <a href="'+url+'">'+url+'</a>');
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
			var url = $('#repository').val();
			var title = $('#issue-title').val();
			window.open(url + "/new/?title=" + encodeURIComponent(title) + "&body=" + encodeURIComponent(body));
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
		$('#repository').change(function() {
			checkLength();
		});

		/** Github Settings */
		$('#issuetemplate-access-token-save').click(function() {
			var token = $('#issuetemplate-access-token').val();
			$('h2.github-settings .icon-checkmark-color').addClass('hidden');
			$('h2.github-settings .icon-loading-small').removeClass('hidden');
			$.post(OC.generateUrl('/apps/issuetemplate/token'),
				{'token' : token}
			).done(function(response) {
				$('div.github-settings').toggleClass('hidden');
				$('h2.github-settings .icon-checkmark-color').removeClass('hidden');
				$('h2.github-settings .icon-loading-small').addClass('hidden');
				$('h2.github-settings .icon-error-color').addClass('hidden');
			}).fail(function(response) {
				$('h2.github-settings .icon-checkmark-color').addClass('hidden');
				$('h2.github-settings .icon-loading-small').addClass('hidden');
				$('h2.github-settings .icon-error-color').removeClass('hidden');
			});
		});
		$('h2.github-settings').click(function () {
			$('div.github-settings').toggleClass('hidden');
		});

		$('#issue-title').change(function () {
			var repo = $('#repository').val();
			$.get(
				OC.generateUrl('/apps/issuetemplate/find?repository='+repo+'&search='+$(this).val())
			).done(function(response) {
				console.log(response);
			}).fail(function(response) {
				console.log(response);
			});
		});
	});

})(jQuery, OC);
