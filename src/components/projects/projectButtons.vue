<!--
Components : ProjectButtons
Props :
	Users				Array
	project				Object
	showQuit		Boolean
	showDelete	Boolean
-->
<i18n>
{
	"en":{
		"projectdeletesuccess": "Project deleted successfully",
		"projectquitsuccess": "Project quit successfully",
		"delete": "Delete project",
		"quit": "Leave project",
		"confirm": "Confirm",
		"cancel": "Cancel",
		"lastuser": "You are the last user in the project, if you quit, the project is delete.",
		"delproject": "Are you sure you want to delete the project?",
		"quitproject": "Are you sure you want to leave the project?",
		"lastadmin": "You are the last admin, you can choose to define a new admin or let the project without admin."
	},
	"fr": {
		"projectdeletesuccess": "Project supprimé avec succès",
		"projectquitsuccess": "Project quitté avec succès",
		"delete": "Effacer le project",
		"quit": "Quitter le project",
		"confirm": "Confirmer",
		"cancel": "Annuler",
		"lastuser": "Vous êtes le dernier utilisateur, si vous quittez le project, il sera supprimé.",
		"delproject": "Etes-vous sûr de vouloir supprimer le project ?",
		"quitproject": "Etes-vous sûr de vouloir quitter le project ?",
		"lastadmin": "Vous êtes le dernier administrateur, vous pouvez définir un nouveau administrateur ou laisser le project sans."
	}
}
</i18n>

<template>
  <div>
    <div v-if="showQuit" >
      <div v-if="project.is_admin && lastAdmin && confirmQuit">
        <p style="color:red;">
          {{ $t('lastadmin') }}
        </p>
        <project-users :project="project" :users="users" :show-delete-user="false" :show-change-role="true"/>
      </div>
      <div align="right" class="btnproject">
        <p v-if="confirmQuit && !lastAdmin && !lastUser">
          {{ $t("quitproject") }}
        </p>
        <p v-else-if="confirmQuit && lastUser">
          {{ $t('lastuser') }}
        </p>
        <button  type="button"  class="btn btn-danger"  @click="quitProject" v-if="project.users.length > 1">
          {{ confirmQuit?$t('confirm'):$t('quit') }}
        </button>
        <button  v-if="confirmQuit"  type="button"  class="btn btn-secondary"  @click="confirmQuit=!confirmQuit">
          {{ $t('cancel') }}
        </button>
      </div>
    </div>
    <div  v-if="showDelete">
      <div  class="btnproject"  align="right">
        <p v-if="confirmDeletion">
          {{ $t("delproject") }}
        </p>
        <button  type="button"  class="btn btn-danger mr-1"  @click="deleteProject">
          {{ confirmDeletion?$t('confirm'):$t('delete') }}
        </button>
        <button  v-if="confirmDeletion"  type="button"  class="btn btn-secondary"  @click="confirmDeletion=!confirmDeletion">
          {{ $t('cancel') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import ProjectUsers from '@/components/projects/projectUsers'

export default {
	name: 'ProjectButtons',
	components: { ProjectUsers },
	props: {
		project: {
			type: Object,
			required: true,
			default: () => ({})
		},
		users: {
			type: Array,
			required: true,
			default: () => ([])
		},
		showQuit: {
			type: Boolean,
			required: true,
			default: true
		},
		showDelete: {
			type: Boolean,
			required: true,
			default: true
		}
	},
	data () {
		return {
			confirmDeletion: false,
			confirmQuit: false,
			lastAdmin: false,
			lastUser: false
		}
	},
	computed: {
		...mapGetters({
			user: 'currentUser'
		})
	},
	methods: {
		deleteProject () {
			if (!this.confirmDeletion) this.confirmDeletion = true
			else {
				this.$store.dispatch('deleteProject').then(() => {
					this.$snotify.success(this.$t('projectdeletesuccess'))
					this.$router.push('/projects')
				})
					.catch(err => {if (!_.isEmpty(err)) this.$snotify.error(err) })
			}
		},
		quitProject () {
			if (!this.confirmQuit) {
				if (this.project.is_admin) {
					let last = this.users.filter(user => user.is_admin && user.email !== this.user.email)
					this.lastAdmin = !(last.length > 0)
				} else {
					this.lastUser = !(this.users.length > 0)
				}
				this.confirmQuit = true
			} else {
				this.$store.dispatch('removeUserFromProject',{project_id: this.project.project_id, id: this.user.user_id, origin_name: this.user.fullname} ).then(() => {
					this.$snotify.success(this.$t('projectquitsuccess'))
					this.$router.push('/projects')
				})
					.catch(err => {if (!_.isEmpty(err)) this.$snotify.error(err) })
			}
		}
	}
}
</script>

<style scoped>
.btnproject{
	padding: 10px;
}

</style>
