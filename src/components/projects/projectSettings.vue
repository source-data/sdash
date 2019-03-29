<i18n>
{
	"en": {
		"general": "General",
		"user": "Users"
	},
	"fr": {
		"general": "Général",
		"user": "Utilisateurs"
	}
}
</i18n>

<template>
  <div  id="projectSettings"  class="container">
    <div class="row">
      <div class="col-2">
        <nav class="nav nav-pills nav-justified flex-column">
          <a  v-for="(cat,idx) in categories"  :key="idx"  class="nav-link"  :class="(view==cat)?'active':''"  @click="view=cat"> {{ $t(cat) }} </a>
        </nav>
      </div>
      <div class="col-10">
        <project-settings-general v-if="view=='general'" />
        <project-settings-user v-if="view=='user'" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import projectSettingsGeneral from '@/components/projects/projectSettingsGeneral'
import projectSettingsUser from '@/components/projects/projectSettingsUser'

export default {
	name: 'ProjectSettings',
	components: { projectSettingsGeneral, projectSettingsUser },
	data () {
		return {
			view: 'general',
			categories: ['general', 'user']
		}
	},
	computed: {
		...mapGetters({
			project: 'project'
		})
	},
	watch: {
		view () {
			this.$router.push({ query: { view: 'settings', cat: this.view } })
		},
		'$route.query' () {
			this.view = this.$route.query.cat
		}
	},
	created () {
		if (this.categories.indexOf(this.$route.query.cat) > -1) {
			this.view = this.$route.query.cat
		}
	}
}
</script>

<style scoped>
a.nav-link{
	cursor: pointer;
}
</style>
