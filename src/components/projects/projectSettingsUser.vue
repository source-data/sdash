<i18n>
{
	"en": {
		"userlist": "List of users",
		"add_user": "Invite a user",
		"add_series": "Add Figures / Series",
		"download_series": "Show Download Button",
		"send_series": "Add to project / inbox",
		"delete_series": "Remove Figures / Series",
		"write_notifications": "Write Comments",
		"projectuseraddsuccess": "User successfully added to the project",
		"Unknown user": "Unknown user",
    "usersettings": "Project user settings",
		"recipient": "recipient",
		"invitation": "invitation message"
	},
	"fr": {
		"userlist": "Liste d'utilisateurs",
		"add_user": "Inviter un utilisateur",
		"add_series": "Ajouter une étude / série",
		"download_series": "Télécharger une étude / série",
		"send_series": "Ajouter à un projet / inbox",
		"delete_series": "Supprimer une étude / série",
		"write_notifications": "Commenter",
		"projetuseraddsuccess": "L'utilisateur a été ajouté avec succès à l'projet",
		"Unknown user": "Utilisateur inconnu",
    "usersettings": "Réglages des utilisateurs de l'projet",
		"recipient": "destinataire",
		"invitation": "message d'invitation "
	}
}
</i18n>

<template>
  <div class="container">
    <h3  v-if="!form_add_user"  class="pointer"  style="width: 100%">
      {{ $t('userlist') }}
			<button  v-if="project.is_admin"  class="btn btn-secondary float-right"  @click="addUser()"> <v-icon  name="user-plus"  scale="1"  class="mr-2"/> {{ $t('add_user') }} </button>
    </h3>
    <div  v-if="form_add_user"  class="card">
      <div class="card-body">
				
				
        <form @submit.prevent="addUser">
					<!-- TODO: remove firstname / lastname -->
					<div class = 'row'>
						<div class="form-group col-md-6">
							<label for="firstname">{{$t('firstname')}}</label>
							<input type="text" class="form-control" id="firstname"  placeholder="firstname"  v-validate="'required'" data-vv-as="firstname" v-model="new_firstname">
						</div>
						<div class="form-group col-md-6">
							<label for="lastname">{{$t('lastname')}}</label>
							<input type="text" class="form-control" id="lastname"  placeholder="lastname"  v-validate="'required'" data-vv-as="lastname" v-model="new_lastname">
						</div>
					</div>
					<!-- <div class="form-group">
						<label for="recipient">{{$t('recipient')}}</label>
						<input type="email" class="form-control" id="recipient"  :placeholder="'email '+$t('user')"  v-validate="'email'" data-vv-as="email" v-model="new_user_name">
					</div> -->
					<div class="form-group">
						<label for="invitation">{{$t('invitation')}}</label>
						<textarea class="form-control" id="invitation" name="invitation" v-model="invitation"></textarea>
					</div>
					<div class="form-group">
						<button  class="btn btn-primary"  type="submit"  :disabled="!validForm()"> {{ $t('send') }} </button>
						<button  class="btn btn-secondary"  type="reset"  tabindex="0"  @keyup.esc="new_user_name=''"  @click="new_user_name='';form_add_user=!form_add_user"> {{ $t('cancel') }} </button>
					</div>
				</form>
      </div>
    </div>

    <project-users  :project="project"  :users="project.users"  :show-delete-user="true"  :show-change-role="true"/>

  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import ProjectUsers from '@/components/projects/projectUsers'

export default {
	name: 'ProjectSettingsUser',
	components: { ProjectUsers },
	data () {
		return {
			form_add_user: false,
			// new_user_name: '',
			new_firstname: '',
			new_lastname: '',
			new_invitation: ''
		}
	},
	computed: {
		...mapGetters({
			project: 'project',
			users: 'users',
			loggedUser: 'currentUser'
		}),
		invitation: {
			get: function() { return `you have been invited by ${this.loggedUser.fullname} to join the ${this.project.name} project on SDash`},
			set: function(value) {this.new_invitation = value}
		},
		new_user_name () {
			return `${this.new_firstname.replace(/ /g,'').toLowerCase()}.${this.new_lastname.replace(/ /g,'').toLowerCase()}@email.com` 
		}
	},
	created () {
		this.$store.dispatch('getProject', { project_id: this.$route.params.project_id })
	},
	methods: {
		addUser () {
			if (!this.form_add_user) this.form_add_user = true
			else {
				if (this.new_user_name && this.validEmail(this.new_user_name)) {
					this.$store.dispatch('addUserToProject', { email: this.new_user_name, firstname: this.new_firstname, lastname: this.new_lastname, origin_name: this.loggedUser.fullname }).then(() => {
						this.$snotify.success(this.$t('projectuseraddsuccess'))
						// this.new_user_name = ''
						this.new_firstname = ''
						this.new_lastname = ''
						this.form_add_user = false
						this.confirm_delete = ''
					}).catch(res => {
						this.$snotify.error(this.$t(res))
					})
				}
			}
		},
		validEmail (email) {
			var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			return re.test(email)
		},
		validForm () {
			return (this.new_firstname.length > 1 && this.new_lastname.length > 1 && this.validEmail(this.new_user_name))
		},
		patchProject (field) {
			let params = {}
			params[field] = this.project[field]
			this.$store.dispatch('patchProject', params).catch(err => {
				this.$snotify.error(this.$t('sorryerror'))
			})
		}
	}
}

</script>

<style scoped>
label{
	text-transform: capitalize;
}

input::placeholder {
	text-transform: lowercase;
}
fieldset.user_settings {
	border: 1px solid #333;
	padding: 20px;
	background-color: #303030 ;
}

fieldset.user_settings legend{
	padding: 0 20px;
	width: auto;

}
</style>
