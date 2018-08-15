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

import Vue from 'vue';
import VueFormWizard from 'vue-form-wizard';
import VueFormGenerator from 'vue-form-generator';
import AppSelector from './components/appselector.vue';
import DetailSection from './components/detailsection.vue';
import VueClipboard from 'vue-clipboard2';

Vue.use(VueFormWizard);
Vue.use(VueFormGenerator);
Vue.use(VueClipboard);

new Vue({
	el: '#issuetemplate',
	components: {
		'app-selector': AppSelector,
		'detail-section': DetailSection,
	},
	mounted: function () {
		this.resetWizard();
	},
	data: {
		tabs: [],
		model: {},
		preview: {},
		formOptions: {
			validationErrorClass: "has-error",
			validationSuccessClass: "has-success",
			validateAfterChanged: true
		},
		firstTabSchema:{
			fields: [
				{
					type: "input",
					inputType: "text",
					label: "Issue title",
					model: "title",
					//required: true,
					validator: VueFormGenerator.validators.string,
					styleClasses: 'col-sm-12'
				},
				{
					type: "textArea",
					inputType: "text",
					label: "Steps to reproduce",
					model: "stepsToReproduce",
					required: true,
					validator: VueFormGenerator.validators.string,
					styleClasses: 'col-sm-12'
				},
				{
					type: "textArea",
					inputType: "text",
					label: "Expected behaviour",
					model: "expectedBehaviour",
					required: true,
					validator: VueFormGenerator.validators.string,
					styleClasses: 'col-sm-6'
				},
				{
					type: "textArea",
					inputType: "text",
					label: "Actual behaviour",
					model: "actualBehaviour",
					required: true,
					validator: VueFormGenerator.validators.string,
					styleClasses: 'col-sm-6'
				}
			]
		}
	},
	methods: {
		updateSections: function () {
			var self = this;
			self.tabs = [];
			$.ajax({
				url: OC.generateUrl('/apps/issuetemplate/sections/' + this.app),
				method: 'GET',
				success: function (data) {
					self.tabs = data;
				},
				error: function (error) {
					self.tabs = [];
				}
			});
		},
		getAppId: function () {
			if (this.model.component !== null) {
				return this.model.component.id;
			}
			return null;
		},
		selectComponent: function (component) {
			this.model.component = component;
			this.updateSections();
			this.$refs.wizard.nextTab();
		},
		onComplete: function(){
			this.resetWizard();
		},
		resetWizard: function() {
			this.model = {
				component: null,
				title: '',
				stepsToReproduce: '',
				expectedBehaviour: '',
				actualBehaviour: '',
				details: {}
			};
		},
		validateAppSelect: function() {
			return this.getAppId() !== null;
		},
		validateIssueDescription: function(){
			return this.$refs.firstTabForm.validate();
		},
		validateDetails: function(tab){
			var self = this;
			var updates = this.$refs[tab.identifier][0].fetchUpdates();
			if (updates === false) {
				return false;
			}
			this.model.details[tab.identifier] = updates[tab.identifier];
			$.ajax({
				url: OC.generateUrl('/apps/issuetemplate/render'),
				data: this.model,
				method: 'POST',
				success: function (data) {
					self.preview = data;
				}
			});
			return true;
		},
		openIssue: function() {
			var urlComplete = this.model.component.bugs + "/new/?title=" + encodeURIComponent(this.model.title) + "&body=" + encodeURIComponent(this.preview.markdown);
			window.open(urlComplete);
		}
	}
});