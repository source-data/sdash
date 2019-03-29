<i18n scoped>
{
	"en": {
		"projectName": "Project Name",
		"projectDescription": "Project Description",
		"users": "Users",
		"addUser": "Add user",
		"addSeries": "Add figures / series",
		"downloadSeries": "Show download button",
		"sendSeries": "Get figures / series",
		"deleteSeries": "Remove figures / series",
		"writeComments": "Write comments"

	},
	"fr": {
		"projectName": "Nom de l'project",
		"projectDescription": "Description de l'project",
		"users": "Utilisateurs",
		"addUser": "Ajouter un utilisateur",
		"addSeries": "Ajouter une étude / série",
		"downloadSeries": "Télécharger une étude / série",
		"sendSeries": "Ajouter à un project / inbox",
		"deleteSeries": "Supprimer une étude / série",
		"writeComments": "Commenter"
	}
}
</i18n>

<template>
  <div>
    <div  v-if="loading"  class="container">
      <p class="text-center fade"> loading...</p>
    </div>
    <div  v-if="!loading"  class="container">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
          <h3>
            <v-icon  name="book"  scale="2"/>
            <span class="p-2"> {{ project.name }} </span>
            <v-icon  v-if="view=='figures' && project.is_favorite"  name="star"  scale="2"/>
          </h3>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 mb-3">
          <nav class="nav nav-pills nav-fill flex-column flex-lg-row text-center justify-content-lg-end">
            <a  class="nav-link"  :class="(view=='figures')?'active':''"  @click.stop="view='figures'"> Figures </a>
            <a  class="nav-link"  :class="(view=='comments')?'active':''"  @click.stop="view='comments'"> Comments </a>
            <a  class="nav-link"  :class="(view=='settings')?'active':''"  @click.stop="view='settings'"> Settings </a>
          </nav>
        </div>
        <!-- <div class = 'col-md'></div> -->
      </div>
    </div>
    <project-figures v-if="view=='figures'" />
    <project-comments  v-if="view=='comments'"  :id="project.project_id"/>
    <project-settings v-if="view=='settings'" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import projectFigures from '@/components/projects/projectFigures'
import projectComments from '@/components/projects/projectComments'
import projectSettings from '@/components/projects/projectSettings'

export default {
	name: 'Project',
	components: { projectFigures, projectSettings, projectComments },
	data () {
		return {
			view: 'figures',
			newUserName: '',
			loading: false
		}
	},
	computed: {
		...mapGetters({
			project: 'project'
		})
	},
	watch: {
		view () {
			let queryParams = { view: this.view }
			if (this.$route.query.cat !== undefined) queryParams.cat = this.$route.query.cat
			this.$router.push({ query: queryParams })
		},
		'$route.query' () {
			this.view = this.$route.query.view
		}
	},
	created () {
		this.loading = true
		this.$store.dispatch('getProject', { project_id: this.$route.params.project_id }).then(() => {
			this.loading = false
			if (!this.$route.query.view) {
				this.$router.push({ query: { view: 'figures' } })
			}
			this.view = this.$route.query.view
		}).catch(error => {
			this.$snotify.error(error)
			this.$router.push('/projects')
		})
	},
	methods: {
	}
}
</script>

<style scoped>
h3 {
	margin-bottom: 40px;
	float: left;
}

h5.user{
	float: left;
	margin-right: 10px;
}

.icon{
	margin-left: 10px;
}
.pointer{
	cursor: pointer;
}
label{
	margin-left: 10px;
}
a.nav-link{
	cursor: pointer;
}
</style>
