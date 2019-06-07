<i18n>
{
	"en": {
		"figuresuccess": "Figure added successfully",
		"errorfigureloaded": "ERROR: this figure is already loaded"
	},
	"fr": {
		"figuresuccess": "La figure a été ajoutée avec succès",
		"errorfigureloaded": "ERREUR: cette figure est déjà présente"
	}
}
</i18n>
<template>
  <vue-clip :options="options" :on-complete="onComplete">
    <template slot="clip-uploader-action">
      <div>
        <div class="dz-message"><h2> Click or Drag and Drop a <code><b>.jpeg</b></code>, <code><b>.jpg</b></code>, <code><b>.tiff</b></code>, <code><b>.png</b></code> or <code><b>.dar</b></code> file here to upload </h2></div>
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

	data () {
		return {}
	},
	computed: {
		...mapGetters({
			figures: 'figures',
			user: 'currentUser'
		}),
		options(){
			var vm = this
			return {
				url: serverURL+'/dar',
				headers:{'Authorization': 'Bearer '+vm.user.jwt},
				paramName: 'dar'
			}
		}
	},
	methods: {

		onComplete (file, status, xhr) {
			var vm = this
			if (status === 'success'){
				var figure_id = xhr.response
				vm.$store.dispatch('addFigure',figure_id).then(function(){
					vm.$snotify.success(vm.$t('figuresuccess'))
				})
			}
			else this.$snotify.error(this.$t('sorryerror'))
		}
	},

}
</script>
	
<style scoped>
div.dz-message {
	padding: 20px;
	margin: 20px auto;
	width: 600px;
	border: 1px dashed red;
	text-align: center;
}
h2 {
	font-size: 14px;
}
</style>
