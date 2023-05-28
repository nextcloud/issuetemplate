/*
 * @copyright Copyright (c) 2018 Julius Härtl <jus@bitgrid.net>
 *
 * @author Julius Härtl <jus@bitgrid.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

import Vue from 'vue'
import VueFormWizard from 'vue-form-wizard'
import VueFormGenerator from 'vue-form-generator'
import VueClipboard from 'vue-clipboard2'
import { generateFilePath } from '@nextcloud/router'
import App from './App.vue'

// eslint-disable-next-line
__webpack_nonce__ = btoa(OC.requestToken)

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath('issuetemplate', '', 'js/')

Vue.prototype.t = t
Vue.prototype.OCA = OCA

Vue.use(VueFormWizard)
Vue.use(VueFormGenerator)
Vue.use(VueClipboard)

new Vue({
	render: h => h(App),
}).$mount('#issuetemplate')
