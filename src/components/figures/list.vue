<!--
  TODO: Remove settimeout when load figures.
-->
<i18n>
{
	"en": {
		"selectednbfigures": "{count} figure is selected | {count} figures are selected",
		"addproject": "add to a project",
		"download": "download",
		"owner": "owner",
		"created": "creation date",
		"filter": "filter",
		"fromdate": "from",
		"todate": "to",
		"figureputtoproject": "Figures successfully added to the project",
		"includefiguresfromprojects": "include figures in projects",
		"send": "send",
		"delete": "delete",
		"comments": "comments",
		"panels": "panels",
		"figure": "figure",
		"figuressharedsuccess": "figures sent successfully",
		"figuressharederror": "figures could not be sent",
		"addmyfiles": "add to my files",
		"nofigure": "no figure found",
		"figuressend": "figures send to my files",
    "confirmDelete": "are you sure you want to delete {count} figure | are you sure you want to delete {count} figures",
    "confirmdeletepanels": "containing {count} serie? once deleted, you will not be able to re-upload any panels if other users still have access to them. | containing {count} panels? once deleted, you will not be able to re-upload any panels if other users still have access to them.",
    "cancel": "cancel",
		"newproject": "new project..."
	},
	"fr": {
		"selectednbfigures": "{count} étude est sélectionnée | {count} études sont sélectionnées",
		"addprojet": "ajouter à un projet",
		"download": "télécharger",
		"owner": "propriétaire",
		"figuredate": "date de la figure",
		"figuredescription": "description de la figure",
		"filter": "filtrer",
		"fromdate": "de",
		"todate": "a",
		"figureputtoproject": "la figure a été enregistrée dans l'projet avec succès",
		"includefiguresfromprojets": "inclure des séries présentes dans les projets",
		"send": "envoyer",
		"delete": "supprimer",
		"comments": "commentaire",
		"panels": "séries",
		"figure": "étude",
		"figuressharedsuccess": "études ont été envoyées avec succès",
		"figuressharederror": "études n'ont pas pu être envoyée",
		"addmyfiles": "ajouter à mes fichiers",
    "nofigure": "aucune étude trouvée",
		"figuressend": "études envoyées dans votre boîte de réception",
    "confirmDelete": "êtes vous de sûr de vouloir supprimer ? ",
    "cancel": "annuler",
		"newproject": "nouveau projet..."
	}
}
</i18n>

<template>
  <div v-if="!loading">
    <!--button Figure selected -->
    <div class="container-fluid my-3 selection-button-container">
      <span v-if="selectedFiguresNb" class="float-left">
        <span>{{ $tc("selectednbfigures",selectedFiguresNb,{count: selectedFiguresNb}) }}</span>
        <b-dropdown  v-if="!filters.project_id || (project.send_panels || project.is_admin)"  variant="link"  size="sm"  no-caret>
          <template slot="button-content">
            <span> <v-icon  class="align-middle"  name="book"/> </span>
						<br>
						{{ $t("addproject") }}
          </template>
          <b-dropdown-item  v-for="allowedProject in allowedProjects"  :key="allowedProject.id"  @click.stop="addToProject(allowedProject.project_id)">
            {{ allowedProject.name }}
          </b-dropdown-item>
					<b-dropdown-divider />
					<b-dropdown-item>
					<b-button v-b-modal.modalNewProject size="sm"> {{$t('newproject')}} </b-button>
					</b-dropdown-item>
        </b-dropdown>
        <button  v-if="!filters.project_id || project.is_admin"  type="button"  class="btn btn-link btn-sm text-center"  @click="confirmDelete=!confirmDelete">
          <span>  <v-icon  class="align-middle"  name="trash"/>  </span><br> {{ $t("delete") }} 
				</button>
      </span>

      <button  type="button"  class="d-none d-sm-block btn btn-link btn-lg float-right"  @click="showFilters=!showFilters">
        <v-icon  name="search"  scale="2"/>
      </button>
    </div>
    <!--deleteSelectedFigures()-->
    <confirm-button
      v-if="confirmDelete && selectedFiguresNb"
      :btn-primary-text="$t('delete')"
      :btn-danger-text="$t('cancel')"
      :text="$tc('confirmDelete',selectedFiguresNb,{count: selectedFiguresNb})"
      :method-confirm="deleteSelectedFigures"
      :method-cancel="() => confirmDelete=false"
    />
    <form-get-user  v-if="form_send_figure && selectedFiguresNb"  @get-user="sendToUser"  @cancel-user="form_send_figure=false"/>
    <b-table
      class="container-fluid"
      responsive
      striped
      :items="filteredFigures"
      :fields="fields"
      :sort-desc="true"
      :sort-by.sync="sortBy"
      :no-local-sorting="false"
			:dark="false"
      @sort-changed="sortingChanged"
    >
      <template  slot="HEAD_is_selected"  slot-scope="data">
        {{ $t(data.label) }}
      </template>

      <template  slot="HEAD_owner"  slot-scope="data">
        <div  v-if="showFilters"  @click.stop="">
          <input  v-model="filters.owner"  type="search"  class="form-control form-control-sm"  :placeholder="$t('filter')"> <br>
        </div>
        {{ $t(data.label) }}
      </template>

      <template  slot="HEAD_filename"  slot-scope="data">
        <div  v-if="showFilters"  @click.stop="">
          <input  v-model="filters.filename"  type="search"  class="form-control form-control-sm"  :placeholder="$t('filter')"> <br>
        </div>
        {{ $t(data.label) }}
      </template>

      <template  slot="HEAD_nbPanels"  slot-scope="data">
        <div  v-if="showFilters"  @click.stop="">
          <input  v-model="filters.nbPanels"  type="search"  class="form-control form-control-sm"  :placeholder="$t('filter')"> <br>
        </div>
        {{ $t(data.label) }}
      </template>

      <template  slot="HEAD_created"  slot-scope="data">
        <div  v-if="showFilters"  class="form-row"  @click.stop="">
          <div class="col form-inline">
            <div class="form-group">
              <datepicker
                v-model="filters.createdFrom"
                :bootstrap-styling="false"
                :disabled-dates="disabledFromDatesCreated"
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
                v-model="filters.createdTo"
                :bootstrap-styling="false"
                :disabled-dates="disabledToDatesCreated"
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
          <!-- <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateFrom' placeholder="From"> - <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateTo' placeholder="To"> <br/> -->
        </div>
        <br v-if="showFilters">
        {{ $t(data.label) }}
			</template>
      <template  slot="HEAD_modified"  slot-scope="data">
        <div  v-if="showFilters"  class="form-row"  @click.stop="">
          <div class="col form-inline">
            <div class="form-group">
              <datepicker
                v-model="filters.modifiedFrom"
                :bootstrap-styling="false"
                :disabled-dates="disabledFromDatesModified"
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
                v-model="filters.modifiedTo"
                :bootstrap-styling="false"
                :disabled-dates="disabledToDatesModified"
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
          <!-- <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateFrom' placeholder="From"> - <input type = 'search' class = 'form-control form-control-sm' v-model='filters.FigureDateTo' placeholder="To"> <br/> -->
        </div>
        <br v-if="showFilters">
        {{ $t(data.label) }}
      </template>

      <template  slot="HEAD_projects"  slot-scope="data">
        <div  v-if="showFilters"  @click.stop="">
          <input  v-model="filters.projects"  type="search"  class="form-control form-control-sm"  :placeholder="$t('filter')"><br>
        </div>
        {{ $t(data.label) }}
      </template>

      <template  slot="is_selected"  slot-scope="row">
        <b-form-group>
          <b-button  variant="link"  size="sm"  class="mr-2"  @click.stop="showPanels(row)">
            <v-icon  v-if="row.detailsShowing"  class="align-middle"  name="chevron-down"  @click.stop="row.toggleDetails"/>
            <v-icon  v-else  class="align-middle"  name="chevron-right"  @click.stop="row.toggleDetails"/>
          </b-button>
          <b-form-checkbox  v-model="row.item.is_selected"  class="pt-2"  inline  @click.native.stop  @change="toggleSelected(row.item,'figure',!row.item.is_selected)"/>
        </b-form-group>
      </template>

      <!--Infos figure (Panels / Comments / Figure Metadata) -->
      <template  slot="row-details"  slot-scope="row">
        <b-card>
          <div class="row">
            <div class="col-xl-auto mb-4">
              <nav class="nav nav-pills nav-justified flex-column text-center text-xl-left">
                <a  class="nav-link"  :class="(row.item.view==='panels')?'active':''"  @click="row.item.view='panels'">
                  {{ $t('panels') }}
                </a>
                <a  class="nav-link"  :class="(row.item.view==='comments')?'active':''"  @click="loadFiguresComments(row.item)">
                  {{ $t('comments') }}
                </a>
              </nav>
            </div>
            <div  v-if="row.item.view=='panels'"  class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
              <div class="row">
                <div  v-for="panel in row.item.dar.fig"  :key="panel.id"  class="col-sm-12 col-md-12 col-lg-12 col-xl-6 mb-5">
                  <panels-summary :key="panel.id" :panel-i-d="panel.id" :figure-i-d="row.item.id"  />
                </div>
              </div>
            </div>

            <div  v-if="row.item.view=='comments'"  class="col-md-10">
              <comments-and-notifications :id="row.item.id" scope="figures" />
            </div>

            <div v-if="row.item.view=='figure'" class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
              <figure-metadata :id="row.item.id" scope="figures" />
            </div>
          </div>
        </b-card>
      </template>
      <!-- Button next to owner -->
      <template slot="owner" slot-scope="row">
        <div class="ownerContainer">
          <div class="row">
            <div class="col-md-auto">
							<user-icon :name="row.item.owner"></user-icon>
            </div>
            <div class="ownerIcons col-md-auto">
              <!-- <span :class="row.item.SumComments[0]?'selected':''" @click="handleComments(row)">
                <v-icon v-if="row.item.SumComments[0]" class="align-middle" style="margin-right:0" name="comment-dots" />
                <v-icon v-else class="align-middle" style="margin-right:0" name="comment" color="grey" />
              </span> -->
              <a v-if="!filters.project_id || (project.download_panels || project.is_admin)" @click="downloadDar(row.item.id)" class="download">
                <v-icon class="align-middle" style="margin-right:0" name="download" />
              </a>
            </div>
          </div>
        </div>
      </template>

			<template slot="title" slot-scope="data">
				<small>{{data.item.dar.caption.title}}</small>
			</template>

			<template slot="projects" slot-scope="data">
				<span v-for="(p,i) in data.value" :key="i">{{getProjectNameById(p)}}<span v-if="i < data.value.length - 1">, </span></span>
				<span class = 'text-muted' v-if="data.value.length === 0">no project</span>
			</template>

			<template slot="nbPanels" slot-scope="data">
				{{ data.item.dar.fig.length }}
			</template>

			<template slot="create_date" slot-scope="data">
				{{ data.value | formatDateTime }}
			</template>

			<template slot="modified_date" slot-scope="data">
				{{ data.value | formatDateTime }}
			</template>

    </b-table>
    <div  v-if="figures.length===0"  style="text-align:center;"  class="card">
      <div class="card-body">
        {{ $t('nofigure') }}
      </div>
    </div>
		
		<!-- Modal Component -->
		<b-modal id="modalNewProject" title="New project" size="xl" :hide-footer="true" @hide="newProjectCreated" v-if="!project.project_id">
			<new-project :from-modal="true"></new-project>
		</b-modal>
		
		
  </div>
  <div  v-else  class="container-fluid"  style="margin-top: 30px; text-align:center;">
    <pulse-loader  :loading="loading"  color="white"/>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import commentsAndNotifications from '@/components/comments/commentsAndNotifications'
import formGetUser from '@/components/user/getUser'
import panelsSummary from '@/components/figures/panelsSummary'
import figureMetadata from '@/components/figures/figureMetadata.vue'
import newProject from '@/components/projects/newProject'
import Datepicker from 'vuejs-datepicker'

// https://github.com/greyby/vue-spinner
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import ConfirmButton from '@/components/figures/ConfirmButton.vue'
import userIcon from '@/components/user/userIcon'

export default {
	name: 'Figures',
	components: { panelsSummary, Datepicker, commentsAndNotifications, figureMetadata, formGetUser, PulseLoader, ConfirmButton, userIcon, newProject },
	props: {
		project: {
			type: Object,
			required: false,
			default: () => ({})
		}
	},
	data () {
		return {
			pageNb: 1,
			active: false,
			form_send_figure: false,
			fields: [
				{
					key: 'is_selected',
					label: '',
					sortable: false,
					class: 'td_checkbox'
					// thClass: 'd-none d-sm-table-cell'
				},
				{
					key: 'owner',
					label: 'owner',
					thClass: 'text-left capitalize',
					tdClass: 'owner',
					sortable: true
				},
				{
					key: 'filename',
					label: 'file name',
					sortable: true,
					thClass: 'd-none d-md-table-cell d-lg-table-cell capitalize',
					tdClass: 'd-none d-md-table-cell d-lg-table-cell'
				},
				{
					key: 'title',
					label: 'title',
					sortable: true,
					// thClass: 'd-none d-md-table-cell d-lg-table-cell',
					// tdClass: 'd-none d-md-table-cell d-lg-table-cell',
					class: 'td_title'
				},
				{
					key: 'nbPanels',
					label: '# panels',
					sortable: false,
					thClass: 'd-none d-lg-table-cell capitalize',
					tdClass: 'd-none d-lg-table-cell'
				},
				{
					key: 'projects',
					label: 'projects',
					thClass: 'd-none d-sm-table-cell d-md-table-cell d-lg-table-cell capitalize',
					tdClass: 'd-none d-sm-table-cell d-md-table-cell d-lg-table-cell',
					sortable: true
				},
				{
					key: 'create_date',
					label: 'created',
					thClass: 'd-none d-sm-table-cell capitalize',
					tdClass: 'd-none d-sm-table-cell',
					sortable: true
				},
				{
					key: 'modified_date',
					label: 'modified',
					thClass: 'd-none d-sm-table-cell capitalize',
					tdClass: 'd-none d-sm-table-cell',
					sortable: true
				}
			],
			sortBy: 'created',
			sortDesc: true,
			limit: 100,
			optionsNbPages: [5, 10, 25, 50, 100],
			showFilters: false,
			filterTimeout: null,
			filters: {
				owner: '',
				filename: '',
				figureLabel: '',
				projects: '',
				created: '',
				modified: ''
			},
			loading: true,
			send: {
				expected: 0,
				count: 0
			},
			confirmDelete: false,
			selectedPanelsNb: 0
		}
	},
	computed: {
		...mapGetters({
			figures: 'figures',
			projects: 'projects',
			user: 'currentUser'
		}),
		filteredFigures () {
			if (this.project && this.project.project_id){
				let test = _.filter(this.figures, f => f.projects.indexOf(this.project.project_id) > -1)
				return test
			}
			else return this.figures
		},
		totalRows () {
			return this.figures.length
		},
		selectedFiguresNb () {
			return _.filter(this.figures, f => f.is_selected ).length
		},
		disabledToDates: function () {
			let vm = this
			return {
				to: vm.filters.FigureDateFrom,
				from: new Date()
			}
		},
		disabledFromDates: function () {
			return {
				from: new Date()
			}
		},
		allowedProjects () {
			return _.filter(this.projects, a => { return (a.add_panels || a.is_admin) && this.filters.project_id !== a.project_id })
		}
	},

	watch: {
		selectedFiguresNb: {
			handler: function (selectedFiguresNb) {
				if (selectedFiguresNb === 0) {
					this.confirmDelete = false
					this.form_send_figure = false
				}
			}
		},
		send: {
			handler: function (send) {
				if (send.expected === send.count) {
					this.$snotify.success(`${this.send.expected} ${this.$t('figuressend')}`)
				}
			},
			deep: true
		},
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
						owner: '',
						filename: '',
						figureLabel: '',
						projects: '',
						created: '',
						modified: ''

					}
				}
			}
		}
	},

	created () {
		if (this.$route.params.project_id) {
			this.filters.project_id = this.$route.params.project_id
			this.$store.commit('RESET_FIGURE_FLAGS')
			this.setLoading(false)
		} else {
			this.$store.dispatch('getFigures', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit })
				.then(() => { setTimeout(() => this.setLoading(false), 300); })
			this.$store.dispatch('getProjects', { pageNb: 1, limit: 40, sortBy: 'created_time', sortDesc: true })
		}
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
					this.$store.dispatch('getFigures', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit })
				}
			}
		},
		sortingChanged (ctx) {
			// ctx.sortBy   ==> Field key for sorting by (or null for no sorting)
			// ctx.sortDesc ==> true if sorting descending, false otherwise

			this.pageNb = ctx.currentPage
			this.sortBy = ctx.sortBy
			this.sortDesc = ctx.sortDesc
			this.limit = this.figures.length
			this.$store.dispatch('getFigures', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit })
		},
		showPanels (row) {
			if (!row.item.detailsShowing) {
				this.$store.dispatch('getPanels', { figure_id: row.item.id, project_id: this.filters.project_id })
			}
			this.toggleDetails(row)
		},
		toggleDetails (row) {
			this.$store.commit('TOGGLE_DETAILS', { figure_id: row.item.id })
			row.toggleDetails()
		},
		handleComments (row) {
			this.showPanels(row)
			row.item.view = 'comments'
		},
		selectAll (isSelected) {
			this.$store.commit('SELECT_ALL_FIGURES', !isSelected)
			this.figures.allSelected = !this.figures.allSelected
		},
		deleteSelectedFigures () {
			var vm = this
			var i;
			for (i = this.figures.length - 1; i > -1; i--) {
				if (this.figures[i].is_selected) {
					vm.$store.dispatch('deleteFigure', { figure_id: this.figures[i].id, project_id: this.filters.project_id })
				}
			}
		},
		toggleSelected (item, type, isSelected) {
			let index = _.findIndex(this.figures, s => { return s.id === item.id })
			this.$store.commit('TOGGLE_SELECTED_FIGURE', { type: type, index: index, is_selected: isSelected })
		},
		downloadSelectedFigures () {
			var vm = this
			_.forEach(this.figures, function (figure) {
				if (figure.is_selected) {
					vm.$store.dispatch('downloadFigure', { figure_id: figure.id })
				}
			})
		},
		newProjectCreated () {
			let newProject = this.projects[this.projects.length-1];
			this.addToProject(newProject.project_id)
		},
		addToProject (project_id) {
			let figures = _.filter(this.figures, s => s.is_selected)
			if (figures.length) {
				this.$store.commit('PUT_FIGURES_IN_PROJECT',{figures: figures, project_id: project_id})
				this.$snotify.success(this.$t('figureputtoproject'))
			}
		},
		getProjectNameById (project_id){
			return _.map(_.filter(this.projects, p => +p.project_id === +project_id), p => p.name)[0]
		},
		loadFiguresComments (item) {
			item.view = 'comments'
			// this.$store.dispatch('getFiguresComments',{FigureInstanceUID: item.FigureInstanceUID[0]})
		},
		sendToUser (userSub) {
			let figures = _.filter(this.figures, s => { return s.is_selected })
			let figureIds = []; let panelsIds = []
			_.forEach(figures, s => {
				let selectedPanels = _.filter(s.panels, onePanels => { return onePanels.is_selected })
				if (selectedPanels.length === s.panels.length) figureIds.push(s.FigureInstanceUID[0])
				else {
					_.forEach(selectedPanels, onePanels => {
						panelsIds.push({
							FigureInstanceUID: s.FigureInstanceUID[0],
							PanelsInstanceUID: onePanels.PanelsInstanceUID[0]
						})
					})
				}
			})
			if (figureIds.length || panelsIds.length) {
				this.$store.dispatch('sendFigures', { FigureInstanceUIDs: figureIds, PanelsInstanceUIDs: panelsIds, user: userSub, src: this.filters.project_id ? this.filters.project_id : 'inbox' }).then(res => {
					this.$snotify.success(`${figures.length} ${this.$t('figuressharedsuccess')}`)
					if (res.error) this.$snotify.error(`${res.error} ${this.$t('figuressharederror')}`)
				})
			}
		},
		setLoading (val) {
			this.loading = val
		},
		downloadDar (figure_id) {
			console.log('here we need to download something from '+figure_id)
		}
	}
}

</script>

<style scoped>
	table thead th {
		text-transform: capitalize !important;
	}
	td.td_title, th.td_title {
		width: 250px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	select{
		display: inline !important;
	}
	.btn-link {
		font-weight: 400;
		background-color: transparent;
	}

	.btn-link:hover {
		text-decoration: underline;
		background-color: transparent;
		border-color: transparent;
	}

	.ownerContainer{
		position: relative;
		white-space: nowrap;
	}

	.ownerIcons{
		visibility: hidden;
		display: inline;
		cursor: pointer;
	}
	@media (max-width:1024px) {
		.ownerIcons {
			visibility: visible;
			display: inline-block;
		}
	}
	.owner:hover .ownerIcons {
		visibility:visible;
	}
	.ownerIcons > span.selected{
		visibility:visible !important;
	}

	.ownerIcons span{
		margin: 0 3px;
	}

	.selection-button-container{
/*		height: 60px;*/
	}

	.td_checkbox {
		width: auto;
	}

	input.search-calendar{
		width: 100px !important;
	}

	div.calendar-wrapper{
		color: #333;
	}

	a{
		cursor: pointer;
	}

	a.download{
		color: #333;
	}

	a.download:hover{
		color: #000;
	}
</style>
