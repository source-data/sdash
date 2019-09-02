<template>
  <vue-clip :options="options" :on-complete="onComplete">
	<template slot="clip-uploader-action">
	  <div>
		<div class="dz-message"><h2> Click or Drag and Drop a file here to upload </h2></div>
	  </div>
	</template>

	<!-- <template slot="clip-uploader-body" scope="props">
	  <div v-for="file in props.files">
		<img v-bind:src="file.dataUrl" />
		{{ file.name }} {{ file.status }}
	  </div>
	</template> -->

  </vue-clip>
</template>

<script>
import { serverURL } from '@/app_config'
import { mapGetters } from 'vuex'

export default {
	name: 'AttachmentUpload',
	data () {
		return {}
	},

	props: ['panel'],

	computed: {
		...mapGetters({
			figures: 'figures',
			user: 'currentUser'
		}),
		options(){
			return {
				url: `${serverURL}/panels/${this.panel.id}/attachments/`,
				headers:{'Authorization': 'Bearer '+this.user.jwt},
				paramName: 'attachment'
			}
		}
	},
	methods: {

		onComplete (file, status, xhr) {
			console.debug("panel attachment upload", status, file)
			if (status === 'success'){
				// this.$store.dispatch('addFigure',figure_id).then(() => {
				// 	this.$snotify.success(this.$t('figuresuccess'))
				// })
			}
			else {
				this.$snotify.error(xhr.responseText)

			}
		}
	},

}
</script>

<style scoped>
div.dz-message {
	padding: 20px;
	margin: 20px auto;
	width: 80%;
	border: 1px dashed red;
	text-align: center;
}
h2 {
	font-size: 14px;
}
</style>
