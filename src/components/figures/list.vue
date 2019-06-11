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
		"notifications": "notifications",
		"comments": "comments",
		"panels": "panels",
		"figure": "figure",
		"figuressharedsuccess": "figures sent successfully",
		"figuressharederror": "figures could not be sent",
		"addmyfiles": "add to my files",
		"nofigure": "no figure found",
		"figuressend": "figures send to my files",
    "confirmDelete": "are you sure you want to delete {count} figure | are you sure you want to delete {count} figures",
    "confirmRemovefromproject": "are you sure you want to remove {count} figure from this project | are you sure you want to remove {count} figures from this project",
    "confirmdeletepanels": "containing {count} serie? once deleted, you will not be able to re-upload any panels if other users still have access to them. | containing {count} panels? once deleted, you will not be able to re-upload any panels if other users still have access to them.",
    "cancel": "cancel",
		"newproject": "new project...",
		"figuresnotfound": "Error during getting figures",
		"removefromproject":"Remove"
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
		"todate": "à",
		"figureputtoproject": "la figure a été enregistrée dans le projet avec succès",
		"includefiguresfromprojets": "inclure des séries présentes dans les projets",
		"send": "envoyer",
		"delete": "supprimer",
		"notifications": "commentaires",
		"comments": "commentaires",
		"panels": "séries",
		"figure": "étude",
		"figuressharedsuccess": "études ont été envoyées avec succès",
		"figuressharederror": "études n'ont pas pu être envoyée",
		"addmyfiles": "ajouter à mes fichiers",
    "nofigure": "aucune étude trouvée",
		"figuressend": "études envoyées dans votre boîte de réception",
    "confirmDelete": "êtes vous de sûr de vouloir supprimer ? ",
    "confirmRemovefromproject": "êtes vous de sûr de vouloir supprimer du projet? ",		
    "cancel": "annuler",
		"newproject": "nouveau projet...",
		"figuresnotfound": "Erreur pendanr le chargement des figures",
		"removefromproject":"Supprimer"
		
	}
}
</i18n>

<template>
	<div  class="listContainer" >
		<nav-bar v-if="!filters.project_id" />

		<figure-upload v-if="!filters.project_id"></figure-upload>
		<!--button Figure selected -->
		<div id="myHeader" ref="myHeader" :class="isActive ? 'sticky' : ''" class="pt-2">
			<div class="d-flex flex-wrap">
				<div class="container-fluid my-3 selection-button-container">
					<span v-if="selectedFiguresNb || isActive" class="float-left">
						<span>{{ $tc("selectednbfigures",selectedFiguresNb,{count: selectedFiguresNb}) }}</span>
						<b-dropdown  v-if="!filters.project_id"  variant="link"  size="sm"  no-caret :disabled="!selectedFiguresNb">
						<!-- <b-dropdown  v-if="!filters.project_id || (project.send_panels || project.is_admin)"  variant="link"  size="sm"  no-caret> -->
						<template slot="button-content"> <span> <v-icon class="align-middle" name="book"/> </span> <br> {{ $t("addproject") }} </template>

						<b-dropdown-item v-for="allowedProject in allowedProjects" :key="allowedProject.id" @click.stop="addToProject(allowedProject.project_id)"> {{ allowedProject.name }} </b-dropdown-item>
						<b-dropdown-divider v-if="allowedProjects.length" />
						<b-dropdown-item> <b-button v-b-modal.modalNewProject size="sm"> {{$t('newproject')}} </b-button> </b-dropdown-item>
					</b-dropdown>
					<button v-if="!filters.project_id || project.is_admin" type="button" class="btn btn-link btn-sm text-center" :disabled="!selectedFiguresNb" @click="confirmDelete=!confirmDelete"> <span><v-icon class="align-middle" name="trash"/> </span><br> {{ $t("delete") }} </button>
				</span>
				<button type="button" class="d-none d-sm-block btn btn-link btn-lg float-right" @click="showFilters=!showFilters"> <v-icon name="search" scale="2"/> </button>

			</div>

		</div>
		<!--deleteSelectedFigures()-->
		<confirm-button
		v-if="confirmDelete && selectedFiguresNb && !filters.project_id"
		:btn-primary-text="$t('delete')"
		:btn-danger-text="$t('cancel')"
		:text="$tc('confirmDelete',selectedFiguresNb,{count: selectedFiguresNb})"
		:method-confirm="deleteSelectedFigures"
		:method-cancel="() => confirmDelete=false"
		/>
		<confirm-button
		v-if="confirmDelete && selectedFiguresNb && filters.project_id"
		:btn-primary-text="$t('removefromproject')"
		:btn-danger-text="$t('cancel')"
		:text="$tc('confirmRemovefromproject',selectedFiguresNb,{count: selectedFiguresNb})"
		:method-confirm="deleteSelectedFigures"
		:method-cancel="() => confirmDelete=false"
		/>
		<form-get-user  v-if="form_send_figure && selectedFiguresNb"  @get-user="sendToUser"  @cancel-user="form_send_figure=false"/>
		<div v-if="loading">
			<pulse-loader  :loading="loading"  color="white"/>
		</div>

	</div>
	<div ref="figuresList" class="content">
		<b-table
		class="container-fluid"
		:style="{'min-height':(filteredFigures.length) ? '500px':'0px'}"
		responsive
		striped
		:items="filteredFigures"
		:fields="fields"
		:sort-desc="true"
		:sort-by.sync="sortBy"
		:no-local-sorting="true"
		:dark="false"
		@sort-changed="sortingChanged"
		@row-clicked="showDetailsOnRow"
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

		<template  slot="HEAD_title"  slot-scope="data">
			<div  v-if="showFilters"  @click.stop="">
				<input  v-model="filters.title"  type="search"  class="form-control form-control-sm"  :placeholder="$t('filter')"> <br>
			</div>
			{{ $t(data.label) }}
		</template>

		<template  slot="HEAD_create_date"  slot-scope="data">
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
		<template  slot="HEAD_modified_date"  slot-scope="data">
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
		<b-button variant="link" size="sm" class="mr-2" v-if="!filters.project_id || project.is_admin" @click.stop="downloadDar(row.item.id)">
		<v-icon class="align-middle" style="margin-right:0" name="download" />					
	</b-button>
	<b-button variant="link" size="sm" class="mr-2"  @click.stop="handleComments(row)">
	<v-icon v-if="nbComments(row.item.notifications)" class="align-middle" style="margin-right:0" name="comment-dots" />
	<v-icon v-else class="align-middle" name="comment" color="grey" />
</b-button>
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
		<a  class="nav-link"  :class="(row.item.view==='notifications')?'active':''"  @click="loadFiguresComments(row.item)">
			{{ $t('comments') }}
		</a>
	</nav>
</div>
<div  v-if="row.item.view=='panels'"  class="col-sm-12 col-md-12 col-lg-12 col-xl-10">
	<div class="row">
		<div v-for="panel in row.item.dar.fig"  :key="panel.id"  class="col-sm-12 col-md-12 col-lg-12 col-xl-6 mb-5">
			<panels-summary :key="panel.id" :panel-i-d="panel.id" :figure-i-d="row.item.id"  />
		</div>
	</div>
</div>

<div  v-if="row.item.view=='notifications'"  class="col-md-10">
	<comments-and-notifications :id="row.item.id+''" scope="figures" />
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
</div>
</div>
</template>

<template slot="title" slot-scope="data">
<small v-html="data.item.dar.caption.title"></small>
</template>

<template slot="projects" slot-scope="data">
<span v-for="(p,i) in data.value" :key="i">{{getProjectNameById(p)}}<span v-if="i < data.value.length - 1">, </span></span>
<span class = 'text-muted' v-if="data.value.length === 0">no project</span>
</template>

<template slot="nbPanels" slot-scope="data">
{{ data.item.nbPanels }}
</template>

<template slot="create_date" slot-scope="data">
{{ data.value | formatDate }}
</template>

<template slot="modified_date" slot-scope="data">
{{ data.value | formatDate }}
</template>

</b-table>
</div>
		
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
</template>

<script>
import moment from 'moment'
import { mapGetters } from 'vuex'
import commentsAndNotifications from '@/components/notifications/commentsAndNotifications'
import formGetUser from '@/components/user/getUser'
import panelsSummary from '@/components/figures/panelsSummary'
import newProject from '@/components/projects/newProject'
import Datepicker from 'vuejs-datepicker'
import figureUpload from '@/components/figures/figureUpload'

// https://github.com/greyby/vue-spinner
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import ConfirmButton from '@/components/figures/ConfirmButton.vue'
import userIcon from '@/components/user/userIcon'
import navBar from '@/components/navbar'

export default {
	name: 'Figures',
	components: { panelsSummary, Datepicker, commentsAndNotifications, formGetUser, PulseLoader, ConfirmButton, userIcon, newProject, figureUpload,navBar },
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
					thClass: 'd-none d-md-table-cell d-lg-table-cell capitalize',
					tdClass: 'd-none d-md-table-cell d-lg-table-cell td_title',
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
					sortable: false
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
			sortBy: 'create_date',
			sortDesc: true,
			limit: 15,
			// optionsNbPages: [5, 10, 25, 50, 100],
			showFilters: false,
			filterTimeout: null,
			filters: {
				owner: '',
				filename: '',
				title: '',
				figureLabel: '',
				projects: '',
				createdFrom: '',
				createdTo: '',
				modifiedFrom: '',
				modifiedTo: ''
			},
			loading: true,
			send: {
				expected: 0,
				count: 0
			},
			confirmDelete: false,
			selectedPanelsNb: 0,
			isActive: false
		}
	},
	computed: {
		...mapGetters({
			figures: 'figures',
			projects: 'projects',
			user: 'currentUser'
		}),
		filteredFigures () {
			let figures = this.figures;
			_.forEach(this.filters, (value,filter) => {

				if (value) figures = _.filter(figures, f => {
					if (filter === 'project_id' || (f[filter] !== undefined && f[filter].indexOf(value) > -1)) {return true}
					if (filter === 'title') {return f.dar.caption.title.toLowerCase().indexOf(value.toLowerCase()) > -1}
					if (filter === 'createdFrom') {return moment(f.create_date).isSameOrAfter(value)} 
					if (filter === 'createdTo') {return moment(f.create_date).isSameOrBefore(value)} 
					if (filter === 'modifiedFrom') {return moment(f.modified_date).isSameOrAfter(value)} 
					if (filter === 'modifiedTo') {return moment(f.modified_date).isSameOrBefore(value)} 
				})
			})
			// if (this.project && this.project.project_id){
			if (this.filters && this.filters.project_id){
				figures = _.filter(this.figures, f => f.projects.indexOf(this.project.project_id) > -1)
			}
			else{
				figures = _.filter(this.figures, f => f.user_id == this.user.user_id)				
			}
			return figures
		},
		totalRows () {
			return this.figures.length
		},
		selectedFiguresNb () {
			return _.filter(this.figures, f => f.is_selected ).length
		},
		disabledToDatesCreated () {
			let vm = this
			return {
				to: vm.filters.createdFrom || new Date()
			}			
		},
		disabledFromDatesCreated () {
			return {
				from: new Date()
			}
		},
		disabledToDatesModified () {
			let vm = this
			return {
				to: vm.filters.modifiedFrom || new Date()
			}			
		},
		disabledFromDatesModified () {
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
		// filters: {
		// 	handler: function (filters) {
		// 		if (this.filterTimeout) {
		// 			clearTimeout(this.filterTimeout)
		// 		}
		// 		this.filterTimeout = setTimeout(() => this.searchOnline(filters), 300)
		// 	},
		// 	deep: true
		// },
		showFilters: {
			handler: function (showFilters) {
				if (!showFilters) {
					this.filters = {
						owner: '',
						filename: '',
						title: '',
						figureLabel: '',
						projects: '',
						createdFrom: '',
						createdTo: '',
						modifiedFrom: '',
						modifiedTo: ''

					}
				}
			}
		}
	},

	created () {
		var vm = this
		if (this.$route.params.project_id) {
			this.filters.project_id = this.$route.params.project_id
			this.$store.commit('RESET_FIGURE_FLAGS')
		}
		this.$store.dispatch('getFigures', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit, resetDisplay: true })
			.then(() => { setTimeout(() => this.setLoading(false), 300); })
			.catch(function(err){ vm.$snotify.error(err) })

		setTimeout(() => this.$store.dispatch('getProjects'), 300)
	},

	mounted () {
		this.scroll()
	},
	methods: {
		scroll () {
			const _this = this
			
			function getScrollXY() {
				var scrOfX = 0, scrOfY = 0;
				if( typeof( window.pageYOffset ) == 'number' ) {
					//Netscape compliant
					scrOfY = window.pageYOffset;
					scrOfX = window.pageXOffset;
				} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
					//DOM compliant
					scrOfY = document.body.scrollTop;
					scrOfX = document.body.scrollLeft;
				} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
					//IE6 standards compliant mode
					scrOfY = document.documentElement.scrollTop;
					scrOfX = document.documentElement.scrollLeft;
				}
				return [ Math.floor(scrOfX), Math.floor(scrOfY) ];
			}

			function getDocHeight() {
				var D = document;
				return Math.max(
					D.body.scrollHeight, D.documentElement.scrollHeight,
					D.body.offsetHeight, D.documentElement.offsetHeight,
					D.body.clientHeight, D.documentElement.clientHeight
				);
			}
			
			let previousY = 0;
			
			window.onscroll = () => {
				if (this.$route.params.project_id && this.$route.query.view !== 'figures') return

				let bottomOfWindow = (getDocHeight() -20 <  getScrollXY()[1] + window.innerHeight && previousY < getScrollXY()[1]) 
				previousY = getScrollXY()[1];	
				if (bottomOfWindow && !this.loading) {
					this.setLoading(true);
					this.pageNb++
					this.$store.dispatch('getFigures', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit }).then( () => {
						this.setLoading(false);
					})
				}
				if (_this.$refs.myHeader){
					let sticky = _this.$refs.myHeader.offsetTop
					let heightSticky = _this.$refs.myHeader.clientHeight
					let figuresList = _this.$refs.figuresList.offsetTop
					if ((window.pageYOffset) > sticky - heightSticky && !this.isActive) {
						this.isActive = true
					} else if (window.pageYOffset < figuresList - heightSticky) {
						this.isActive = false
					}					
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
			// this.$store.dispatch('getFigures')
			this.$store.dispatch('getFigures', { pageNb: this.pageNb, filters: this.filters, sortBy: this.sortBy, sortDesc: this.sortDesc, limit: this.limit })
		},
		showPanels (row) {
			this.toggleDetails(row)
		},
		toggleDetails (row) {
			this.$store.commit('TOGGLE_DETAILS', { figure_id: row.item.id })
			row.toggleDetails()
		},
		showDetailsOnRow (row) {
			this.$store.commit('TOGGLE_DETAILS', { figure_id: row.figure_id })
			row._showDetails = !row._showDetails
		},
		
		handleComments (row) {
			this.showPanels(row)
			row.item.view = 'notifications'
		},
		nbComments (notifications) {
			let idx = _.findIndex(notifications, n => n.event_type === 'comment')
			return idx > -1
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
					if (!this.filters || !this.filters.project_id){
						vm.$store.dispatch('deleteFigure',{figure_id: this.figures[i].id})
					}
					else{
						vm.$store.dispatch('removeFigureFromProject',{figure_id: this.figures[i].id, project_id: +this.filters.project_id}).then(function(){
							vm.filters.inc =1;
						})					
					}
				}
			}
		},
		toggleSelected (item, type, isSelected) {
			let index = _.findIndex(this.figures, s => { return s.id === item.id })
			this.$store.commit('TOGGLE_SELECTED_FIGURE', { type: type, index: index, is_selected: isSelected })
		},
		// downloadSelectedFigures () {
		// 	var vm = this
		// 	_.forEach(this.figures, function (figure) {
		// 		if (figure.is_selected) {
		// 			vm.$store.dispatch('downloadFigure', { figure_id: figure.id,jwt:vm.user.jwt })
		// 		}
		// 	})
		// },
		newProjectCreated () {
			let newProject = this.projects[this.projects.length-1];
			this.addToProject(newProject.project_id)
		},
		addToProject (project_id) {
			var vm =this;
			let figures = _.filter(vm.figures, s => s.is_selected)
			if (figures.length) {
				_.forEach(figures,function(fig){
					vm.$store.dispatch('putFiguresInProject',{figure_id: fig.figure_id, project_id: project_id, user_id: vm.user.user_id}).then(function(project){
						vm.$store.commit('PUT_PROJECT_IN_FIGURE',project);
					})					
				})
				vm.$snotify.success(vm.$t('figureputtoproject'))
			}
		},
		getProjectNameById (project_id){
			return _.map(_.filter(this.projects, p => +p.project_id === +project_id), p => p.name)[0]
		},
		loadFiguresComments (item) {
			item.view = 'notifications'
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
			this.$store.dispatch('downloadDar',{figure_id:figure_id,jwt:this.user.jwt})
		}
	}
}

</script>

<style scoped>

div.listContainer{
/*	min-height: 500px;*/
}

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

	td.td_checkbox {
		width: auto;
		white-space: nowrap;
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
	
  .sticky {
    position: fixed;
    top: 70px;
    width: 100%;
    background: #555;
    z-index: 5;
    opacity: 0.95;
  }
  .sticky + .content {
    padding-top: 70px;
  }
	
</style>
