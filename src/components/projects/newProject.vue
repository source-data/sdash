<i18n>
{
	"en": {
		"projectname": "project name",
		"projectdescription": "project description",
		"projecturl": "project url",
		"users": "users",
		"create": "create",
		"cancel": "cancel",
		"projectalreadyexists": "Sorry, the project already exists"
	},
	"fr": {
		"projectname": "nom de l'projet",
		"projectdescription": "description de l'projet",
		"projecturl": "url du projet",
		"users": "utilisateurs",
		"create": "créer",
		"cancel": "annuler",
		"projectalreadyexists": "Désolé, le projet existe déjà"
	}
}
</i18n>

<template>
  <div class="container">
    <!-- <nav-bar /> -->
    <h3 v-if="!fromModal">{{ displayName }}</h3>
    <form @submit.prevent="createProject">
      <fieldset>
        <div class="row">
          <div class="col-xs-12 col-sm-3">
            <dt>{{ $t('projectname') }}</dt>
          </div>
          <div class="col-xs-12 col-sm-9">
            <dd>
              <input  v-model="project.name"  type="text"  :placeholder="$t('projectname')"  class="form-control"  maxlength="255">
            </dd>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-3">
            <dt>{{ $t('projectdescription') }}</dt>
          </div>
          <div class="col-xs-12 col-sm-9">
            <dd>
              <textarea  v-model="project.description"  rows="5"  class="form-control"  :placeholder="$t('projectdescription')"  maxlength="2048"/>
            </dd>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-3">
            <dt>{{ $t('projecturl') }}</dt>
          </div>
          <div class="col-xs-12 col-sm-9">
            <dd>
              <input  v-model="project.url"  type="text"  :placeholder="$t('projecturl')"  class="form-control"  maxlength="255">
            </dd>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-3">
            <dt>{{ $t('users') }}</dt>
          </div>
          <div class="col-xs-12 col-sm-9">
            <dd>
              <h5 class="user">
                <span  v-for="user in project.users"  :key="user.user_id"  class="badge badge-secondary mr-1">
									{{ user.name }}
									<span class="icon pointer" @click="deleteUser(user)"> <v-icon name="times" /> </span>
                </span>
              </h5>
              <h5 class="user">
                <div class="input-group mb-3">
                  <input  v-model="newUserEmail"  type="text"  class="form-control form-control-sm" id="newuserinput" placeholder="email"  aria-label="Email"  @keydown.enter.prevent="checkUser">
                  <div class="input-group-append">
                    <button  id="button-addon2"  class="btn btn-outline-secondary btn-sm"  type="button"  title="add user"  @click="checkUser()" :disabled="!newUserEmail"> <v-icon name="plus" /> </button>
                  </div>
                </div>
              </h5>
            </dd>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <div class="row">
					<div class = "offset-3">
							<button  type="submit"  class="btn btn-primary mr-2"  :disabled="!project.name"> {{ $t('create') }}  </button>
							<button  type="button"  class="btn btn-secondary" @click="cancel"> {{ $t('cancel') }}  </button>
					</div>
        </div>
      </fieldset>
    </form>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'newProject',
	components: { },
	props: {
		fromModal: {
			required: false,
			type: Boolean
		}
	},
	data () {
		return {
			project: {
				project_id: '',
				name: '',
				description: '',
				url: '',
				users: []
			},
			newUserEmail: ''
		}
	},
	created () {
		// this.$store.commit('RESET_PROJECTS')
		this.project.users.push({
			id: this.currentUser.user_id,
			email: this.currentUser.email,
			name: this.currentUser.fullname,
			is_admin: true
		})
	},
	computed: {
		...mapGetters ({
			currentUser: 'currentUser',
			projects: 'projects',
		}),
		displayName () {
			return (!this.project.project_id) ? 'New project' : this.project.name
		}
	},
	methods: {
		deleteUser (user) {
			let index = this.project.users.findIndex(i => i.email === user.email)
			if (index > -1) this.project.users.splice(index, 1)
		},
		checkUser () {
			let vm = this
			let idx = _.findIndex(vm.project.users, u => { return u.email === vm.newUserEmail })
			if (vm.newUserEmail && idx === -1) {
				this.$store.dispatch('checkUser',{email: vm.newUserEmail}).then( res => {
					if (!res){
						this.$snotify.error("Sorry, the user is unknown")
						return;
					}
					let emailIdx = _.findIndex(vm.project.users, u => u.email === res.email);
					if (emailIdx === -1){
						res.is_admin = false
						vm.project.users.push(res);						
					}
					else {
						this.$snotify.warning("The user is already member of the project")
					}
					vm.newUserEmail = '';
					document.getElementById('newuserinput').focus()
				}, error => {
					this.$snotify.error("Sorry, the user is unknown")
					vm.newUserEmail = '';
					document.getElementById('newuserinput').focus()
				})
			}
			else{
				this.$snotify.warning("The user is already member of the project")				
			}
		},
		createProject () {
			let postValues = {
				name: this.project.name,
				description: this.project.description,
				url: this.project.url,
				users: this.project.users
			}
			var existing = _.findIndex(this.projects,function(p){return p.name == postValues.name;});
			if (existing<-1){
				this.$snotify.error(this.$t('projectalreadyexists'))
				return;	
			}
			this.$store.dispatch('createProject', postValues).then(project => {
				if (this.fromModal){
					this.$root.$emit('bv::hide::modal','modalNewProject')
				}
				else this.$router.push('/projects/' + project.project_id)
			}, error => {
				this.$snotify.error(error)
			})
		},
		cancel () {
			if (this.fromModal){
				this.$root.$emit('bv::hide::modal','modalNewProject')
			}
			else this.$router.push('/projects')
		}
	}
}

</script>

<style scoped>
dt{
	text-transform: capitalize;
}

h3 {
	margin-bottom: 40px;
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
fieldset.user_settings {
	border: 1px solid #333;
	padding: 20px;
	background-color: #303030 ;
}

fieldset.user_settings legend{
	padding: 0 20px;
	width: auto;
}
dt{
	text-align: right;
}
</style>
