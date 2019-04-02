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
        <div class="dz-message"><h2> Click or Drag and Drop a <code><b>.dar</b></code> file here to upload </h2></div>
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
import { mapGetters } from 'vuex'
export default {

	data () {
		return {
			options: {
				url: 'https://sourcedata-robin.vital-it.ch/sdash/parse_dar.php',
				paramName: 'file'
			}
		}
	},
	computed: {
		...mapGetters({
			figures: 'figures',
			user: 'currentUser'
		})
	},
	methods: {
		onComplete (file, status, xhr) {
			if (status === 'success'){
				let figure = JSON.parse(xhr.response)
				figure.owner = this.user.fullname
				let idx = _.findIndex(this.figures, f => f.filename === figure.filename);
				if (idx > -1) {
					this.$snotify.error(this.$t("errorfigureloaded"))
					return
				}
				this.$store.dispatch('addFigure',figure)
				this.$snotify.success(this.$t('figuresuccess'))
			}
			else this.$snotify.error(this.$t('sorryerror'))
		}
	}

}
</script>
	
<style scoped>
div.dz-message {
	padding: 20px;
	margin: 20px auto;
	width: 500px;
	border: 1px dashed red;
}
h2 {
	font-size: 14px;
}
</style>
