<template>
	<div id="issuetemplate" class="section">
		<h2 class="inlineblock">
			{{ t('issuetemplate', 'Issue reporting') }}
		</h2>

		<p>
			For reporting potential security issues please see
			<a href="https://nextcloud.com/security/">https://nextcloud.com/security/</a>
		</p>

		<div>
			<form-wizard ref="wizard"
				shape="tab"
				color="#0082c9"
				error-color="#a94442"
				@on-complete="onComplete">
				<div slot="title" />

				<tab-content title="Affected component" icon="icon-category-customization icon-invert" :before-change="validateAppSelect">
					<app-selector @select="selectComponent" />
				</tab-content>

				<tab-content title="Issue description" icon="icon-user icon-invert" :before-change="validateIssueDescription">
					<vue-form-generator ref="firstTabForm"
						:model="model"
						:schema="firstTabSchema"
						:options="formOptions" />
				</tab-content>

				<tab-content v-for="tab in tabs"
					v-show="model.component"
					:key="tab.identifier"
					:title="tab.title"
					icon="icon-settings icon-invert"
					:before-change="()=>validateDetails(tab)">
					<detail-section :ref="tab.identifier"
						:app="getAppId()"
						:section="tab.identifier"
						:model="model" />
				</tab-content>

				<tab-content v-if="tabs.length" title="Check issue report" icon="icon-checkmark icon-invert">
					<h4>Check your bug report before submitting it</h4>
					<div class="panel-body">
						<p>
							<strong>Please always check if the automatically filled out information is correct and there is nothing important missing, before reporting the issue.</strong>
						</p>

						<p>
							<strong>This report will be submitted to nextcloud/server</strong>
						</p>

						<div id="preview" v-html="preview.rendered" />
						<textarea id="preview" v-model="preview.markdown" />
					</div>
				</tab-content>

				<template slot="footer" slot-scope="props">
					<div class="wizard-footer-left">
						<button v-if="props.activeTabIndex > 0 && !props.isLastStep" @click="props.prevTab()">
							Previous
						</button>
					</div>
					<div class="wizard-footer-right">
						<button v-if="!props.isLastStep" class="primary" @click="props.nextTab()">
							Next
						</button>
						<button v-if="props.isLastStep" v-clipboard:copy="preview.markdown" class="">
							Copy issue text
						</button>
						<button v-if="props.isLastStep && preview.markdown && preview.markdown.length<4096" class="" @click="openIssue()">
							Open a new issue
						</button>
					</div>
				</template>
			</form-wizard>
		</div>
	</div>
</template>
<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import VueFormGenerator from 'vue-form-generator'

import AppSelector from './components/AppSelector.vue'
import DetailSection from './components/DetailSection.vue'

export default {
	name: 'App',
	components: {
		'app-selector': AppSelector,
		'detail-section': DetailSection,
	},
	data() {
		return {
			color: '#aaaaaa',
			tabs: [],
			model: {},
			preview: {},
			formOptions: {
				validationErrorClass: 'has-error',
				validationSuccessClass: 'has-success',
				validateAfterChanged: true,
			},
			firstTabSchema: {
				fields: [
					{
						type: 'input',
						inputType: 'text',
						label: 'Issue title',
						model: 'title',
						// required: true,
						validator: VueFormGenerator.validators.string,
						styleClasses: 'col-sm-12',
					},
					{
						type: 'textArea',
						inputType: 'text',
						label: 'Steps to reproduce',
						model: 'stepsToReproduce',
						required: true,
						validator: VueFormGenerator.validators.string,
						styleClasses: 'col-sm-12',
					},
					{
						type: 'textArea',
						inputType: 'text',
						label: 'Expected behaviour',
						model: 'expectedBehaviour',
						required: true,
						validator: VueFormGenerator.validators.string,
						styleClasses: 'col-sm-6',
					},
					{
						type: 'textArea',
						inputType: 'text',
						label: 'Actual behaviour',
						model: 'actualBehaviour',
						required: true,
						validator: VueFormGenerator.validators.string,
						styleClasses: 'col-sm-6',
					},
				],
			},
		}
	},
	mounted() {
		this.resetWizard()
	},
	methods: {
		async updateSections() {
			this.tabs = []
			try {
				const { data } = await axios.get(generateUrl('/apps/issuetemplate/sections/' + this.getAppId()))
				this.tabs = data
			} catch (e) {
				console.error(e)
				this.tabs = []
			}
		},
		getAppId() {
			if (this.model.component !== null) {
				return this.model.component.id
			}
			return null
		},
		selectComponent(component) {
			this.model.component = component
			this.updateSections()
			this.$refs.wizard.nextTab()
		},
		onComplete() {
			this.resetWizard()
		},
		resetWizard() {
			this.model = {
				component: null,
				title: '',
				stepsToReproduce: '',
				expectedBehaviour: '',
				actualBehaviour: '',
				details: {},
			}
		},
		validateAppSelect() {
			return this.getAppId() !== null
		},
		validateIssueDescription() {
			return this.$refs.firstTabForm.validate()
		},
		async validateDetails(tab) {
			const updates = this.$refs[tab.identifier][0].fetchUpdates()
			if (updates === false) {
				return false
			}
			this.model.details[tab.identifier] = updates[tab.identifier]
			try {
				const { data } = await axios.post(generateUrl('/apps/issuetemplate/render'), this.model)
				this.preview = data
			} catch (e) {
				console.error(e)
			}
			return true
		},
		openIssue() {
			const urlComplete = this.model.component.bugs + '/new/?title=' + encodeURIComponent(this.model.title) + '&body=' + encodeURIComponent(this.preview.markdown)
			window.open(urlComplete)
		},
	},
}
</script>
<style lang="scss">
@import './css/style.scss';
</style>
