<!--
  - @copyright Copyright (c) 2018 Julius Härtl <jus@bitgrid.net>
  -
  - @author Julius Härtl <jus@bitgrid.net>
  -
  - @license GNU AGPL version 3 or any later version
  -
  -  This program is free software: you can redistribute it and/or modify
  -  it under the terms of the GNU Affero General Public License as
  -  published by the Free Software Foundation, either version 3 of the
  -  License, or (at your option) any later version.
  -
  -  This program is distributed in the hope that it will be useful,
  -  but WITHOUT ANY WARRANTY; without even the implied warranty of
  -  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  -  GNU Affero General Public License for more details.
  -
  -  You should have received a copy of the GNU Affero General Public License
  -  along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
  -->
<template>
	<div id="details">
		<div class="details-section">
			<vue-form-generator :schema="schema" :model="model" :options="formOptions" @validated="onValidated" />
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			app: String,
			section: String,
		},
		mounted: function () {
			this.updateDetails();
		},
		watch: {
			app: function(value, oldValue) {
				if (typeof value !== 'undefined') {
					this.updateDetails();
				}
			}
		},
		methods: {
			updateDetails: function () {
				let self = this;
				$.ajax({
					url: OC.generateUrl('/apps/issuetemplate/details/' + this.app + '/' + this.section),
					method: 'GET',
					success: function (data) {
						self.model = data.model;
						self.schema = data.schema;
					},
					error: function (error) {
						console.log(error);
						self.apps = [];
					}
				});
			},
			onValidated(isValid, errors) {
				this.isValid = isValid;
			},
			fetchUpdates: function() {
				if (this.isValid) {
					return this.model;
				} else {
					return false;
				}
			}
		},
		data: function () {
			return {
				isValid: false,
				schema: {},
				model: {},
				formOptions: {
					validateAfterChanged: true,
					validateAfterLoad: true,
				}
			}
		}
	}
</script>