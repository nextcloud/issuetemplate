<div id="issuetemplate" class="section">
	<h2 class="inlineblock"><?php p($l->t('Issue reporting')); ?></h2>

	<p>
		<?php p($l->t("For reporting potential security issues please see")); ?>
		<a href="https://nextcloud.com/security/">https://nextcloud.com/security/</a>
	</p>

		<div>

			<form-wizard @on-complete="onComplete"
						 shape="tab"
						 color="<?php p(\OC::$server->getThemingDefaults()->getColorPrimary()); ?>"
						 error-color="#a94442"
						 ref="wizard">
				<div slot="title"></div>


				<tab-content title="Affected component" icon="icon-category-customization icon-invert" :before-change="validateAppSelect">
					<app-selector v-on:select="selectComponent"></app-selector>
				</tab-content>

				<tab-content title="Issue description" icon="icon-user icon-invert" :before-change="validateIssueDescription">
					<vue-form-generator :model="model" :schema="firstTabSchema" :options="formOptions" ref="firstTabForm"></vue-form-generator>
				</tab-content>

				<tab-content v-for="tab in tabs" v-if="model.component" :key="tab.identifier" :title="tab.title" icon="icon-settings icon-invert" :before-change="()=>validateDetails(tab)">
					<detail-section :app="getAppId()" :section="tab.identifier" :ref="tab.identifier" :model="model"></detail-section>
				</tab-content>

				<tab-content title="Check issue report" icon="icon-checkmark icon-invert" v-if="tabs.length">
					<h4>Check your bug report before submitting it</h4>
					<div class="panel-body">
						<p>
							<strong><?php p($l->t("Please always check if the automatically filled out information is correct and there is nothing important missing, before reporting the issue.")); ?></strong>
						</p>

						<p>
							<strong><?php p($l->t("This report will be submitted to nextcloud/server")); ?></strong>
						</p>

						<div id="preview" v-html="preview.rendered">

						</div>
						<textarea id="preview" v-html="preview.markdown">

						</textarea>
					</div>
				</tab-content>

				<template slot="footer" slot-scope="props">
					<div class="wizard-footer-left">
						<button v-if="props.activeTabIndex > 0 && !props.isLastStep" @click="props.prevTab()">Previous</button>
					</div>
					<div class="wizard-footer-right">
						<button v-if="!props.isLastStep" @click="props.nextTab()" class="primary">Next</button>
						<button v-if="props.isLastStep" v-clipboard:copy="preview.markdown" class="">Copy issue text</button>
						<button v-if="props.isLastStep && preview.markdown && preview.markdown.length<4096" @click="openIssue()"  class="">Open a new issue</button>
					</div>
				</template>

			</form-wizard>


		</div>
</div>


