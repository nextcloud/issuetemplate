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
	<div id="app-selector">
		<div v-for="item in apps" :key="item.id">
			<h3>{{ item.title }}</h3>

			<div class="affected-components">
				<div v-for="component in item.items"
					:key="component.id"
					class="affected-component"
					@click="selectComponent(component)">
					<div class="logo">
						<img :src="component.icon">
					</div>
					<p>{{ component.name }}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'AppSelector',
	data() {
		return {
			apps: {},
			selected: this.selected,
		}
	},
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/issuetemplate/components'))
			console.debug(response)
			this.apps = response.data
		} catch (e) {
			console.error(e)
			this.apps = []
		}
	},
	methods: {
		selectComponent(component) {
			this.$emit('select', component)
		},
	},
}
</script>
