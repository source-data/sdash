<i18n>
{
	"en":{
		"projectname": "Project name",
		"projecturl": "Project URL",
		"projectdescription": "Project description",
		"notification": "Notifications"
	},
	"fr": {
		"projectname": "Nom de l'projet",
		"projecturl": "URL du projet",
		"projectdescription": "Description de l'projet",
		"notification": "Notifications"
	}
}
</i18n>

<template>
  <div class="container">
    <dl>
      <dt>{{ $t('projectname') }}</dt>
      <dd>
        <div v-if="edit.name === '-1'">
          {{ project.name }} 
					<span  v-if="project.is_admin && edit.name === '-1'"  class="icon-edit"  @click="edit.name=project.name"> <v-icon name="pencil-alt" /> </span>
        </div>
        <div v-if="edit.name !== '-1'">
          <form @submit.prevent="updateProject">
            <div class="input-group mb-2">
              <div>
                <input  v-model="edit.name"  type="text"  class="form-control">
              </div>
              <div class="input-group-append">
                <button  class="btn btn-primary"  type="submit"> {{ $t('update') }} </button>
                <button  class="btn btn-secondary"  type="reset"  tabindex="0"  @keyup.esc="edit.name = '-1'"  @click="edit.name = '-1'"> {{ $t('cancel') }} </button>
              </div>
            </div>
          </form>
        </div>
      </dd>
      <dt>
        {{ $t('projectdescription') }}
				<span  v-if="project.is_admin && edit.description === '-1'"  class="icon-edit float-right"  @click="edit.description=project.description"> <v-icon name="pencil-alt" /> </span>
      </dt>
      <dd class="project_description">
        <div v-if="edit.description === '-1'">
          <p  v-for="(p,pidx) in formattedProjectDescription"  :key="pidx"  class="my-0"> {{ p }} </p>
        </div>
        <div v-if="edit.description !== '-1'">
          <form @submit.prevent="updateProject">
            <div class="">
              <div>
                <textarea  v-model="edit.description"  rows="6"  class="form-control"/>
              </div>
              <div>
                <button  class="btn btn-primary"  type="submit"> {{ $t('update') }} </button>
                <button  class="btn btn-secondary"  type="reset"  tabindex="0"  @keyup.esc="edit.description = '-1'"  @click="edit.description = '-1'"> {{ $t('cancel') }} </button>
              </div>
            </div>
          </form>
        </div>
      </dd>
      <dt>{{ $t('projecturl') }}</dt>
      <dd>
        <div v-if="edit.url === '-1'">
          {{ project.url }} 
					<i class = 'muted' v-if="!project.url">{{$t('empty')}}</i>
					<span  v-if="project.is_admin && edit.url === '-1'"  class="icon-edit"  @click="edit.url=project.url"> <v-icon name="pencil-alt" /> </span>
        </div>
        <div v-if="edit.url !== '-1'">
          <form @submit.prevent="updateProject">
            <div class="input-group mb-2">
              <div>
                <input  v-model="edit.url"  type="text"  class="form-control">
              </div>
              <div class="input-group-append">
                <button  class="btn btn-primary"  type="submit"> {{ $t('update') }} </button>
                <button  class="btn btn-secondary"  type="reset"  tabindex="0"  @keyup.esc="edit.url = '-1'"  @click="edit.url = '-1'"> {{ $t('cancel') }} </button>
              </div>
            </div>
          </form>
        </div>
      </dd>
    </dl>
    <project-buttons  :project="project"  :users="project.users"  :show-quit="true"  :show-delete="project.is_admin"/>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import ProjectButtons from '@/components/projects/projectButtons'

export default {
	name: 'ProjectSettingsGeneral',
	components: { ProjectButtons },
	data () {
		return {
			edit: {
				name: '-1',
				description: '-1',
				url: '-1'
			}
		}
	},
	computed: {
		...mapGetters({
			project: 'project',
			users: 'users',
			user: 'currentUser'
		}),
		formattedProjectDescription () {
			return this.project.description.split('\n')
		}
	},
	created () {
		this.$store.dispatch('getProject', { project_id: this.$route.params.project_id })
		this.$store.dispatch('getUsers')
	},
	methods: {
		updateProject () {
			if (!this.project.is_admin) {
				this.$snotify.error(this.$t('permissiondenied'))
				return
			}
			let params = {}
			_.forEach(this.edit, (v, k) => {
				if (v === '-1') return
				if (this.project[k] !== v) {
					params[k] = v
				}
			})
			params.origin_name = this.user.fullname
			this.$store.dispatch('patchProject', params).then(() => {
				this.$snotify.success(this.$t('projectupdatesuccess'))
				this.edit.name = '-1' 
				this.edit.description = '-1'
				this.edit.url = '-1' 
			}).catch(() => {
				this.$snotify.error(this.$t('sorryerror'))
			})
		}
	}
}

</script>

<style scoped>
dd span.icon-edit, dt span.icon-edit {
	margin: 0 10px;
	cursor: pointer;
}

dl {
	font-size: 125%;
}
dl label {
	font-size: 100%;
	margin-left: 20px;
}

dd.project_description{
	border: 1px solid #333;
	height: 10em;
	padding: 10px;
}

</style>

