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


Vue.use(VueFormWizard)
Vue.use(VueFormGenerator)

new Vue({
	el: '#app',
	components: {
		'issuetemplate-app-selector': AppSelector,
		'app-selector': AppSelector,

	},
	mounted: function () {
		var self = this;

		$.ajax({
			url: OC.linkTo('issuetemplate', 'applist'),
			method: 'GET',
			success: function (data) {
				self.apps = data;
			},
			error: function (error) {
				console.log(error);
				self.apps = [
					{
						id: 'core',
						title: 'Core components',
						items: [
							{
								id: 'server',
								title: 'Nextcloud server',
								icon: OC.imagePath('core', 'logo'),
								reportRepo: 'https://github.com/nextcloud/server'
							},
							{
								id: 'android',
								title: 'Nextcloud Android App',
								icon: OC.imagePath('core', 'logo'),
								reportRepo: 'https://github.com/nextcloud/server'
							}
						]
					}
				];
			}
		});
	},
	data: {
		apps: {},
		model:{
			title:'',
			stepsToReproduce:'',
			expectedBehaviour:'',
			actualBehaviour:'',
		},
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
					required: true,
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
					styleClasses: 'col-sm-12'
				}
			]
		},
		secondTabSchema:{
			fields:[
				{
					type: "input",
					inputType: "text",
					label: "Street name",
					model: "streetName",
					required:true,
					validator:VueFormGenerator.validators.string,
					styleClasses:'col-xs-9'
				},
				{
					type: "input",
					inputType: "text",
					label: "Street number",
					model: "streetNumber",
					required:true,
					validator:VueFormGenerator.validators.string,
					styleClasses:'col-xs-3'
				},
				{
					type: "input",
					inputType: "text",
					label: "City",
					model: "city",
					required:true,
					validator:VueFormGenerator.validators.string,
					styleClasses:'col-xs-6'
				},
				{
					type: "select",
					label: "Country",
					model: "country",
					required:true,
					validator:VueFormGenerator.validators.string,
					values:['United Kingdom','Romania','Germany'],
					styleClasses:'col-xs-6'
				},
			]
		}
	},
	methods: {
		onComplete: function(){
			alert('Yay. Done!');
		},
		validateAppSelect: function() {
			return true;
		},
		validateFirstTab: function(){
			return this.$refs.firstTabForm.validate();
		},
		validateSecondTab: function(){
			return this.$refs.secondTabForm.validate();
		},
		validateLogMessages: function() {
			// FIXME: validation
			return true;
		},
		prettyJSON: function(json) {
			if (json) {
				json = JSON.stringify(json, undefined, 4);
				json = json.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>');
				return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
					var cls = 'number';
					if (/^"/.test(match)) {
						if (/:$/.test(match)) {
							cls = 'key';
						} else {
							cls = 'string';
						}
					} else if (/true|false/.test(match)) {
						cls = 'boolean';
					} else if (/null/.test(match)) {
						cls = 'null';
					}
					return '<span class="' + cls + '">' + match + '</span>';
				});
			}
		}
	}
})