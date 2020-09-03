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
			<vue-form-generator :schema="schema"
				:model="model"
				:options="formOptions"
				@validated="onValidated" />
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	props: {
		app: {
			type: String,
			required: true,
		},
		section: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			isValid: false,
			schema: {},
			model: {},
			formOptions: {
				validateAfterChanged: true,
				validateAfterLoad: true,
			},
		}
	},
	watch: {
		app(value, oldValue) {
			if (typeof value !== 'undefined') {
				this.updateDetails()
			}
		},
	},
	mounted() {
		this.updateDetails()
	},
	methods: {
		async updateDetails() {
			try {
				const { data } = await axios.get(generateUrl('/apps/issuetemplate/details/' + this.app + '/' + this.section))
				this.model = data.model
				this.schema = data.schema
			} catch (e) {
				console.error(e)
				this.apps = []
			}
		},
		onValidated(isValid, errors) {
			this.isValid = isValid
		},
		fetchUpdates() {
			if (this.isValid) {
				return this.model
			} else {
				return false
			}
		},
	},
}
</script>
