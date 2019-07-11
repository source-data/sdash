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
		"hasadd": "has add",
		"hasgranted": "has granted",
		"hasremoved": "has removed",
		"adminrights": "admin rights",
		"hasleft": "has left",
		"hascreated": "has created",
		"hasedited": "has edited",
		"includenotifications": "project's history",
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
		"includenotifications": "historique du projet",
		"addproject": "a mis en favori",
		"removeproject": "a enlevé des favories"
	}
}
</i18n>

<template>
    <div>
    
    </div>

</template>

<script>
import { mapGetters } from 'vuex'

export default {
	name: 'SlimHistory',
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
				note_id: null,
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
		this.getHistory()
		if (this.project.project_id) this.$store.dispatch('getUsers')
	},
	methods: {
		getHistory () { console.log("get History of Id : " + this.id);
			if (this.scope === 'project') {
				this.$store.dispatch('getProjectComments', { type: type }).then(() => {
					let container = this.$el.querySelector('#project_comment_container')
					container.scrollTop = container.scrollHeight
				})
			} else if (this.scope === 'figures') {
				this.$store.dispatch('getFigureComments', { id: this.id, type: '' }).then(() => { console.log("history: success")
					// let container = this.$el.querySelector('#figure_' + this.id.replace(/\./g, '_') + '_comment_container')
					// container.scrollTop = container.scrollHeight
				}).catch((err) => {console.log(err)})
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
    .slim-comments--comment {
        background-color: #d6d6d6;
        overflow:hidden;
        margin-bottom: 0.5rem;
        border-radius: 0.25rem 0.25rem 0 0;
        color:#444;
    }

    .slim-comments--comment header {
        display:flex;
        flex-wrap:nowrap;
        justify-content: space-between;
        padding: 0.25em 0.5em;
        background-color: #c3c3c3;
    }

    .slim-comments--content {
        padding: 0.25em 0.5em;
    }

    .slim-comments--comment footer {
        text-align:right;
        padding-top:4px;
    }

    .slim-comments--edit {
        border:none;
        background-color:transparent;
        font-size: 0.85em;
        color: #05866d;
    }

    .slim-comments--edit-icon {
        margin-right:4px;
        position: relative;
        top: -2px;
    }

    hr {
        border-top: 2px solid rgba(255, 255, 255, 0.52);
    }
</style>
