<!--
Components : ProjectUsers
Props :
	Users						Array
	project						Object
	showDeleteUser	Boolean
	showChangeRole	Boolean
-->
<i18n>
{
	"en": {
		"username": "User name",
		"user": "user",
		"changerole": "change role to",
		"projectuserdeletesuccess": "Access to the project has been successfully removed",
		"usernotsettoadmin": "User no longer has admin rights",
		"usersettoadmin": "User has admin rights",
		"warningtoggleadmin": "Warning! do you really want to revoke your admin role? ",
		"remove": "Remove user"
	},
	"fr": {
		"username": "Utilisateur",
		"user": "Utilisateur",
		"changerole": "changer le rôle pour",
		"projectuserdeletesuccess": "L'accès à l'project a été supprimé avec succès",
		"usernotsettoadmin": "L'utilisateur n'a plus de droits admin",
		"usersettoadmin": "L'utilisateur a des droits admin",
		"warningtoggleadmin": "Attention ! Voulez-vous vraiment renoncer à vos droits admin ?  ",
		"remove": "Retirer l'utilisateur"
	}
}
</i18n>
<template>
  <div class="user-table-container">
    <table class="table">
      <thead>
        <tr>
          <th>{{ $t('username') }}</th>
          <th v-if="project.is_admin" />
        </tr>
      </thead>
      <tbody>
        <tr  v-for="user in users"  :key="user.name">
          <td>
            {{ user.name }}
            <span  v-if="user.is_admin"  style="color:#13B98B"> (Admin) </span>
          </td>
          <td  v-if="project.is_admin"  class="showOnTrHover text-right">
            <div  v-if="confirmDelete!=user.name"  class="user_actions">
              <a  v-if="showChangeRole && !confirmResetAdmin"  @click.stop="toggleAdmin(user)">
								{{ $t('changerole') }} {{ (user.is_admin)?$t('user'):"admin" }}
                <v-icon	name="user" />
              </a>
							<a  v-if="project.is_admin && showDeleteUser && project.users.length > 1 && !confirmResetAdmin"  class="text-danger"  style="margin-left: 20px"  @click.stop="deleteUser(user)">
                {{ $t('remove') }}
                <v-icon name="trash" />
              </a>
            </div>
            <div v-if="confirmDelete==user.name">
              <div class="btn-group">
                <button  type="button"  class="btn btn-sm btn-danger"  @click.stop="deleteUser(user)"> {{ $t('confirm') }} </button>
								<button  type="button"  class="btn btn-sm btn-secondary"  @click.stop="confirmDelete=''"> {{ $t('cancel') }} </button>
              </div>
            </div>
            <div v-if="confirmResetAdmin==user.name">
              <span class="text-danger mr-2"> {{ $t("warningtoggleadmin") }} </span>
              <div class="btn-group">
                <button  type="button"  class="btn btn-sm btn-danger"  @click.stop="toggleAdmin(user)"> {{ $t('confirm') }} </button>
								<button  type="button"  class="btn btn-sm btn-secondary"  @click.stop="confirmResetAdmin=''"> {{ $t('cancel') }} </button>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
	name: 'ProjectUsers',
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
		showDeleteUser: {
			type: Boolean,
			required: true,
			default: true
		},
		showChangeRole: {
			type: Boolean,
			required: true,
			default: true
		}
	},
	data () {
		return {
			confirmDelete: '',
			confirmResetAdmin: ''
		}
	},
	computed: {
		...mapGetters({
			user: 'currentUser'
		})
	},
	watch: {
		users: {
			handler: function () {
				this.$store.dispatch('getProject', { project_id: this.$route.params.project_id })
			},
			deep: true
		}
	},
	methods: {
		toggleAdmin (user) {
			if (this.user.user_id === user.id && !this.confirmResetAdmin) {
				this.confirmResetAdmin = user.name
				return
			}
			this.$store.dispatch('toggleProjectUserAdmin', {project_id: this.project.project_id, user: user}).then(() => {
				this.$store.dispatch('getProject', { project_id: this.project.project_id })
				let message = (user.is_admin) ? this.$t('usersettoadmin') : this.$t('usernotsettoadmin')
				this.$snotify.success(message)
			})
				.catch(err => {if (!_.isEmpty(err)){
					this.$snotify.error(err) 
					user.is_admin = !user.is_admin
				} 
				})
		},
		deleteUser (user) {
			if (this.confirmDelete !== user.name) this.confirmDelete = user.name
			else {
				this.$store.dispatch('removeUserFromProject', { name: user.name, id: user.id, project_id: this.project.project_id }).then(() => {
					this.$snotify.success(this.$t('projectuserdeletesuccess'))
					this.confirmDelete = ''
				})
					.catch(err => {if (!_.isEmpty(err)) this.$snotify.error(err) })

			}
		}
	}
}
</script>

<style scoped>
div.user-table-container{
	min-height: 200px;
	padding: 25px 0;
}
a {
	cursor: pointer;
}
td.showOnTrHover div.user_actions{
	visibility: hidden;
}

tr:hover  td.showOnTrHover div.user_actions{
	visibility: visible;
}
</style>
