/* eslint-disable */

<i18n>
{
	"en": {
		"newproject": "New project",
		"Figure #": "figure #",
		"User #": "user #",
		"Message #": "message #",
		"Date": "date",
		"LastEvent": "last event",
		"share": "Invite user"
	},
	"fr": {
		"newproject": "Nouvel project",
		"Figure #": "# figures",
		"User #": "# utilisateurs",
		"Message #": "# messages",
		"Date": "date",
		"LastEvent": "dern. evnt",
		"share": "Inviter un utilisateur"
	}
}
</i18n>

<template>
  <div class="container-fluid">
    <div  class="my-3 selection-button-container"  style=" position: relative;">
      <h3>
        <router-link  to="projects/new"  class="nav-link"  active-class="active"  style="display: inline">
          <v-icon  name="plus"  class="mr-2"/>{{ $t('newproject') }}
        </router-link>
        <button  type="button"  class="btn btn-link btn-lg float-right"  style="position: absolute; right: 20px;top: 0"  @click="showFilters=!showFilters">
          <v-icon  name="search"  scale="2"/>
        </button>
      </h3>
    </div>
    <b-table
      striped
      :items="myprojects"
      :fields="fields"
      :sort-desc="true"
      :sort-by.sync="sortBy"
      :dark="false"
      @row-clicked="selectProject"
			id="projectList"
    >
      <template  slot="HEAD_name"  slot-scope="data">
        <div  v-if="showFilters"  @click.stop="">
          <input  v-model="filters.name"  type="search"  class="form-control form-control-sm"  :placeholder="$t('filter')"> <br>
        </div>
        {{ $t(data.label) }}
      </template>
      <template  slot="HEAD_number_of_figures"  slot-scope="data">
        {{ $t(data.label) }}
      </template>
      <template  slot="HEAD_number_of_users"  slot-scope="data">
        {{ $t(data.label) }}
      </template>
      <template  slot="HEAD_number_of_comments"  slot-scope="data">
        {{ $t(data.label) }}
      </template>
      <template  slot="HEAD_created_time"  slot-scope="data">
        <div  v-if="showFilters"  class="form-row"  @click.stop="">
          <div class="col form-inline">
            <div class="form-group">
              <datepicker
                v-model="filters.CreateDateFrom"
                :bootstrap-styling="false"
                :disabled-dates="disabledFromCreateDates"
                input-class="form-control form-control-sm  search-calendar"
                :calendar-button="false"
                calendar-button-icon=""
                wrapper-class="calendar-wrapper"
                :placeholder="$t('fromDate')"
                :clear-button="true"
                clear-button-icon="fa fa-times"
              />
            </div>
          </div>

          <div class="col form-inline">
            <div class="form-group">
              <datepicker
                v-model="filters.CreateDateTo"
                :bootstrap-styling="false"
                :disabled-dates="disabledToCreateDates"
                input-class="form-control form-control-sm search-calendar"
                :calendar-button="false"
                calendar-button-icon=""
                wrapper-class="calendar-wrapper"
                :placeholder="$t('toDate')"
                :clear-button="true"
                clear-button-icon="fa fa-times"
              />
            </div>
          </div>
          <!-- <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateFrom' placeholder="From"> - <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateTo' placeholder="To"> <br> -->
        </div>
        <br v-if="showFilters">
        {{ $t(data.label) }}
      </template>
      <template  slot="HEAD_last_event_time"  slot-scope="data">
        <div  v-if="showFilters"  class="form-row"  @click.stop="">
          <div class="col form-inline">
            <div class="form-group">
              <datepicker
                v-model="filters.EventDateFrom"
                :bootstrap-styling="false"
                :disabled-dates="disabledFromEventDates"
                input-class="form-control form-control-sm  search-calendar"
                :calendar-button="false"
                calendar-button-icon=""
                wrapper-class="calendar-wrapper"
                :placeholder="$t('fromDate')"
                :clear-button="true"
                clear-button-icon="fa fa-times"
              />
            </div>
          </div>

          <div class="col form-inline">
            <div class="form-group">
              <datepicker
                v-model="filters.EventDateTo"
                :bootstrap-styling="false"
                :disabled-dates="disabledToEventDates"
                input-class="form-control form-control-sm search-calendar"
                :calendar-button="false"
                calendar-button-icon=""
                wrapper-class="calendar-wrapper"
                :placeholder="$t('toDate')"
                :clear-button="true"
                clear-button-icon="fa fa-times"
              />
            </div>
          </div>
          <!-- <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateFrom' placeholder="From"> - <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateTo' placeholder="To"> <br> -->
        </div>
        <br v-if="showFilters">
        {{ $t(data.label) }}
      </template>
      <template  slot="name"  slot-scope="data">
        <div class="nameContainer">
          {{ data.item.name }}
        </div>
      </template>
			<template slot="number_of_figures" slot-scope="data">
				{{ data.item.figures.length }}
			</template>
			<template slot="number_of_users" slot-scope="data">
				{{ data.item.users.length }}
			</template>
			<template slot="number_of_comments" slot-scope="data">
				{{ data.item.comments.length }}
			</template>
      <template  slot="create_date"  slot-scope="data">
        {{ data.item.create_date | formatDate }}
      </template>
      <template  slot="last_event"  slot-scope="data">
        {{ data.item.last_event | formatDate }}
      </template>
      <template  slot="row-details"  slot-scope="row">
        <b-card>
          <dl><dt>Description</dt><dd>{{ row.item.description }}</dd></dl>
        </b-card>
      </template>
    </b-table>
  </div>
</template>
<script>

import { mapGetters } from 'vuex'
import Datepicker from 'vuejs-datepicker'

export default {
	name: 'Projects',
	components: { Datepicker },
	data () {
		return {
			pageNb: 1,
			active: false,
			fields: [
				{
					key: 'name',
					label: 'name',
					thClass: 'capitalize',
					tdClass: 'name',
					sortable: true
				},
				{
					key: 'number_of_figures',
					label: 'Figure #',
					sortable: true,
					thClass: 'd-none d-sm-table-cell capitalize',
					tdClass: 'd-none d-sm-table-cell'
				},
				{
					key: 'number_of_users',
					label: 'User #',
					sortable: true,
					thClass: 'd-none d-md-table-cell capitalize',
					tdClass: 'd-none d-md-table-cell'
				},
				{
					key: 'number_of_comments',
					label: 'Messages #',
					sortable: true,
					thClass: 'd-none d-lg-table-cell capitalize',
					tdClass: 'd-none d-lg-table-cell'
				},
				{
					key: 'create_date',
					label: 'Date',
					sortable: true,
					thClass: 'd-none d-sm-table-cell capitalize',
					tdClass: 'd-none d-sm-table-cell'
				},
				{
					key: 'last_event',
					label: 'LastEvent',
					sortable: true,
					thClass: 'd-none d-lg-table-cell capitalize',
					tdClass: 'd-none d-lg-table-cell'
				}
			],
			sortBy: 'created_time',
			sortDesc: true,
			limit: 100,
			optionsNbPages: [5, 10, 25, 50, 100],
			showFilters: false,
			filterTimeout: null,
			filters: {
				name: '',
				number_of_figures: '',
				number_of_users: '',
				number_of_comments: '',
				CreateDateFrom: '',
				CreateDateTo: '',
				EventDateFrom: '',
				EventDateTo: ''
			}

		}
	},
	computed: {
		...mapGetters({
			projects: 'projects',
			user: 'currentUser'
		}),
		totalRows () {
			return this.projects.length
		},
		disabledToCreateDates: function () {
			let vm = this
			return {
				to: vm.filters.CreateDateFrom,
				from: new Date()
			}
		},
		disabledFromCreateDates: function () {
			return {
				from: new Date()
			}
		},
		disabledToEventDates: function () {
			let vm = this
			return {
				to: vm.filters.EventDateFrom,
				from: new Date()
			}
		},
		disabledFromEventDates: function () {
			return {
				from: new Date()
			}
		},
		myprojects () {
			let vm = this
			return _.filter(this.projects, p => {
				return _.findIndex(p.users, u => u.id === vm.user.user_id) > -1
			})
		}

	},
	watch: {
		filters: {
			handler: function (filters) {
				if (this.filterTimeout) {
					clearTimeout(this.filterTimeout)
				}
				this.filterTimeout = setTimeout(() => this.searchOnline(filters), 300)
			},
			deep: true
		},
		showFilters: {
			handler: function (showFilters) {
				if (!showFilters) {
					this.filters = {
					}
				}
			}
		}
	},
	created () {
		this.$store.dispatch('getProjects', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit })
	},
	mounted () {
		this.scroll()
	},
	methods: {
		scroll () {
			window.onscroll = () => {
				let bottomOfWindow = Math.floor((document.documentElement.scrollTop || document.body.scrollTop)) + Math.floor(window.innerHeight) === document.documentElement.offsetHeight
				if (bottomOfWindow) {
					this.pageNb++
					this.$store.dispatch('getProjects', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit })
				}
			}
		},
		toggleDetails (row) {
			this.$store.commit('TOGGLE_ALBUM_DETAILS', { projectId: row.item.project_id })
			row.toggleDetails()
		},
		handleComments (index, entity) {
			this.projects[index][entity] = !this.projects[index][entity]
		},
		deleteSelectedProjects () {
			var vm = this
			var i
			for (i = this.projects.length - 1; i > -1; i--) {
				if (this.projects[i].is_selected) {
					vm.$store.dispatch('deleteProject', { project_id: this.projects[i].project_id })
					vm.$delete(vm.projects, i)
				}
			}
		},
		downloadSelectedProjects () {
			var vm = this
			_.forEach(this.projects, function (project) {
				if (project.is_selected) {
					vm.$store.dispatch('downloadProject', { project_id: project.project_id })
				}
			})
		},
		selectProject (item) {
			if (item.project_id) {
				this.$router.push('/projects/' + item.project_id)
			}
		}
	}

}

</script>

<style scoped>

select{
	display: inline !important;
}
.btn-link {
	font-weight: 400;
	color: white;
	background-color: transparent;
}

.btn-link:hover {
	color: #c7d1db;
	text-decoration: underline;
	background-color: transparent;
	border-color: transparent;
}

.selection-button-container{
	height: 60px;
}

.td_checkbox {
	width: 150px;
}

input.search-calendar{
	width: 100px !important;
}

div.calendar-wrapper{
	color: #333;
}

.nameContainer{
	position: relative;
	white-space: nowrap;
	cursor: pointer;
}

.nameIcons{
	margin-left: 10px;
	visibility: hidden;
	display: inline;
	cursor: pointer;
}

.name:hover .nameIcons {
	visibility:visible;
}

.nameIcons > span.selected{
	visibility:visible !important;
}

.nameIcons span{
	margin: 0 3px;
}

</style>
