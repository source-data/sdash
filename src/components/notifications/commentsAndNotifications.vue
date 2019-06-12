<i18n>
{
	"en": {
		"commentpostsuccess": "comment posted successfully",
		"commentupdatesuccess": "comment updated successfully",
		"commentdeletesuccess": "comment deleted successfully",
		"imported": "imported",
		"removed": "removed",
		"thefigure": "the figure",
		"thefigures": "the figures",
		"hasadd": "has added",
		"hasgranted": "has granted",
		"hasremoved": "has removed",
		"adminrights": "admin rights",
		"hasleft": "has left",
		"hascreated": "has created",
		"hasedited": "has edited",
		"includenotifications": "include notifications",
		"addproject": "add as favorite",
		"removeproject": "remove as favorite"
	},
	"fr" : {
		"commentpostsuccess": "le commentaire a été posté avec succès",
		"commentupdatesuccess": "le commentaire a été mis à jour avec succès",
		"commentdeletesuccess": "le commentaire a été effacé avec succès",
		"imported": "a importé",
		"removed": "a supprimé",
		"thefigure": "la figure",
		"thefigures": "la série",
		"hasadd": "a ajouté",
		"hasgranted": "a attribué",
		"hasremoved": "a retiré",
		"adminrights": "des droits admin",
		"hasleft": "a quitté",
		"hascreated": "a créé",
		"hasedited": "a édité",
		"includenotifications": "inclure les notifications",
		"addproject": "a mis en favori",
		"removeproject": "a enlevé des favories"
	}
}
</i18n>

<template>
  <div class="container">
    <div class="row justify-content-center">
      <p  v-if="scope === 'project'"  class="col-sm-12 col-md-10 offset-md-1 text-right">
        <label class="mr-2"> {{ $t('includenotifications') }} </label> 
				<toggle-button  v-model="includeNotifications"  :labels="{checked: 'Yes', unchecked: 'No'}"  :sync="true"  @change="getComments"/>
      </p>

      <div  :id="container_id"  class="card col-sm-12 col-md-10 offset-md-1 pt-3 pb-3"  style="max-height: 600px; overflow-y: scroll;">
        <div  v-for="(comment, idx) in comments"  :key="idx">
          <!-- Comments -->

          <div  v-if="comment.event_type === 'comment'"  class="card mt-3 ml-5 mr-5">
            <div class="card-header">
              <v-icon name="user" class="mr-2" /> {{ comment.origin_name }}<span class="float-right"> {{ comment.post_date|formatDate }} </span>
            </div>
            <div  class="card-body">
							<span class = 'float-right pointer' v-if="comment.origin_name===user.fullname" @click="editComment(idx)"><v-icon name="pencil-alt" /></span>
              <p  v-for="(p,pidx) in splitComment(comment.comment)"  :key="pidx"  class="my-0" :class="comment.origin_name===user.fullname?'pr-3':''"> {{ p }} </p>
            </div>
          </div>

          <!-- Notifications -->

          <div  v-if="comment.event_type == 'mutation'"  class="card col-sm-10 offset-sm-2 bg-secondary mt-3 ml-5 mr-5">
            <div class="d-flex">
              <!-- IMPORT_STUDY, REMOVE_STUDY : -->
              <div  v-if="comment.mutation_type === 'IMPORT_FIGURE'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('imported') }} {{ $t('thefigure') }} {{ comment.comment }}
              </div>
              <div  v-if="comment.mutation_type === 'REMOVE_FIGURE'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('removed') }} {{ $t('thefigure') }} {{ comment.comment }}
              </div>


              <!-- ADD_USER, ADD_ADMIN, REMOVE_USER, PROMOTE_ADMIN, DEMOTE_ADMIN -->
              <div  v-if="comment.mutation_type === 'ADD_USER'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hasadd') }} {{ $t('theuser') }} {{ comment.comment }}
              </div>
              <div  v-if="comment.mutation_type === 'ADD_ADMIN'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hasadd') }} {{ $t('theadmin') }} {{ comment.comment }}
              </div>
              <div  v-if="comment.mutation_type === 'REMOVE_USER'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('removed') }} {{ $t('theuser') }} {{ comment.comment }}
              </div>
              <div  v-if="comment.mutation_type === 'PROMOTE_ADMIN'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hasgranted') }} {{ $t('adminrights') }} {{ $t('to') }} {{ comment.comment }}
              </div>
              <div  v-if="comment.mutation_type === 'DEMOTE_ADMIN'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hasremoved') }} {{ $t('adminrights') }} {{ $t('to') }} {{ comment.comment }}
              </div>

              <!-- LEAVE_ALBUM -->
              <div  v-if="comment.mutation_type === 'LEAVE_PROJECT'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hasleft') }}
              </div>

              <!-- CREATE_ALBUM -->
              <div  v-if="comment.mutation_type === 'CREATE_PROJECT'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hascreated') }} {{ $t('theproject') }}
              </div>

              <!-- EDIT_ALBUM -->
              <div  v-if="comment.mutation_type === 'EDIT_PROJECT'"  class="p-2 flex-grow-1 bd-highlight">
                <i>{{ comment.origin_name }}</i> {{ $t('hasedited') }} {{ $t('theproject') }}
              </div>


              <div class="bd-highlight">
                <small style="white-space: nowrap"> {{ comment.post_date|formatDate }} </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4 justify-content-center">
      <div class="col-sm-12 col-md-10 offset-md-1">
        <form  v-if="scope === 'figures' || project.is_admin"  @submit.prevent="addComment">
          <div class="row justify-content-center">
            <div class="col-9 mb-2">
              <textarea  v-model="newComment.comment"  class="form-control"  rows="6"  placeholder="comment here..."  maxlength="1024"/>
            </div>
            <div class="col-auto">
              <button  type="submit"  class="btn btn-lg btn-primary"  :disabled="newComment.comment.length < 2 && !newComment.post_date">
                <v-icon  name="paper-plane"  class="mr-2"/> {{ (newComment.post_date) ? ((newComment.comment.length) ? $t('update') : $t('delete')) : $t('send') }}
							</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
	name: 'CommentsAndNotifications',
	props: {
		scope: {
			type: String,
			required: true
		},
		id: {
			type: String,
			required: true
		}
	},
	data () {
		return {
			newComment: {
				comment: '',
				to_user: '',
				post_date: null
			},
			includeNotifications: false
		}
	},
	computed: {
		...mapGetters({
			project: 'project',
			figures: 'figures',
			projectNotifications: 'projectNotifications',
			users: 'users',
			user: 'currentUser'
		}),
		comments () {
			if (this.scope === 'project') return this.projectNotifications

			let figureIdx = _.findIndex(this.figures, s => { return +s.id === +this.id })
			if (figureIdx > -1) {
				return this.figures[figureIdx].notifications
			}
			return []
		},
		container_id () {
			return (this.scope === 'project') ? 'project_comment_container' : 'figure_' + this.id.replace(/\./g, '_') + '_comment_container'
		}
	},
	created () {
		this.getComments()
		if (this.project.project_id) this.$store.dispatch('getUsers')
	},
	methods: {
		addComment () {
			let vm = this;
			if (this.newComment.comment.length > 2 || this.newComment.post_date) {
				if (this.newComment.comment.indexOf('@') > -1) {
					if (this.users.length) { // project
						_.forEach(this.users, user => {
							if (this.newComment.comment.indexOf(user.user_name) > -1) {
								this.newComment.to_user = user.user_name
								this.newComment.comment = this.newComment.comment.replace('@' + user.user_name, '').trim()
							}
						})
					} else {
						let atIdx = this.newComment.comment.indexOf('@')
						let end = this.newComment.comment.substr(atIdx).length
						let match = this.newComment.comment.substr(atIdx).match(/\s/)
						if (match) end = match.index
						this.newComment.to_user = this.newComment.comment.substr(atIdx + 1, end - 1)
						this.$store.dispatch('checkUser', this.newComment.to_user).then(res => {
							if (res) {
								this.newComment.comment = this.newComment.comment.replace('@' + this.newComment.to_user, '').trim()
								this.newComment.to_user = res
							} else {
								this.newComment.to_user = ''
							}
						})
					}
				}
				if (this.scope === 'project') {
					this.$store.dispatch('postProjectComment', {comment: vm.newComment.comment, to_user: vm.newComment.to_user, origin_name: vm.user.fullname, post_date: vm.newComment.post_date}).then(() => {
						if (!vm.newComment.comment) this.$snotify.success(this.$t('commentdeletesuccess'))
						else if (vm.newComment.post_date) this.$snotify.success(this.$t('commentupdatesuccess'))
						else this.$snotify.success(this.$t('commentpostsuccess'))
						this.newComment.comment = ''
						this.newComment.to_user = ''
						this.newComment.post_date = ''
					}).catch(res => {
						this.$snotify.error(this.$t('sorryerror') + ': ' + res)
						this.newComment.comment = ''
						this.newComment.to_user = ''
						this.newComment.post_date = ''
					})
				}
				else if (this.scope === 'figures') {
					this.$store.dispatch('postFigureComment', { id: this.id, comment: vm.newComment.comment,note_id: vm.newComment.note_id, to_user: vm.newComment.to_user, origin_name: vm.user.fullname, post_date: vm.newComment.post_date }).then(() => {
						if (!vm.newComment.comment) this.$snotify.success(this.$t('commentdeletesuccess'))
						else if (vm.newComment.post_date) this.$snotify.success(this.$t('commentupdatesuccess'))
						else this.$snotify.success(this.$t('commentpostsuccess'))
						this.newComment.comment = ''
						this.newComment.to_user = ''
						this.newComment.post_date = ''							
						this.newComment.note_id = ''							
					}).catch(res => {
						this.$snotify.error(this.$t('sorryerror') + ': ' + res)
						this.newComment.comment = ''
						this.newComment.note_id = ''							
						this.newComment.to_user = ''
					})
				}
			}
		},
		getComments () {
			let type = (this.includeNotifications) ? '' : 'comments'
			if (this.scope === 'project') {
				this.$store.dispatch('getProjectComments', { type: type }).then(() => {
					let container = this.$el.querySelector('#project_comment_container')
					container.scrollTop = container.scrollHeight
				})
			} else if (this.scope === 'figures') {
				this.$store.dispatch('getFigureComments', { id: this.id, type: type }).then(() => {
					let container = this.$el.querySelector('#figure_' + this.id.replace(/\./g, '_') + '_comment_container')
					container.scrollTop = container.scrollHeight
				})
			}
		},
		splitComment (comment) {
			return comment.split('\n')
		},
		editComment (idx) {
			this.newComment.comment = this.comments[idx].comment
			this.newComment.note_id = this.comments[idx].note_id
			this.newComment.to_user = this.comments[idx].to_user
			this.newComment.post_date = this.comments[idx].post_date
			
		}
	}
}

</script>

<style scoped>

</style>
