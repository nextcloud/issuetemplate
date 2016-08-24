/**
 * Nextcloud - issuetemplate
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julius Härtl <jus@bitgrid.net>
 * @copyright Julius Härtl 2016
 */

(function ($, OC) {

	$(document).ready(function () {
		$('#submit-issue').click(function (e) {
			e.preventDefault();
			var body = "";
			body += $('#issue-description').val();
			body += "\n\n";
			body += $('#issue-serverinfo').val();
			window.open("https://github.com/nextcloud/server/issues/new?body=" + encodeURIComponent(body));
		});
	});

})(jQuery, OC);