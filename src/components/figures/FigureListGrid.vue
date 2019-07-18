<!--
  TODO: Remove settimeout when load figures.
-->
<template>
	<div>
		<b-container>
			<b-row class="mb-4">
				<b-col>
					<b-input-group
						size="lg"
						prepend="Search"
					>
						<b-form-input></b-form-input>
					</b-input-group>
				</b-col>
			</b-row>
			<b-row class="mb-4">
				<b-col>
					<figure-upload v-if="!filters.project_id"></figure-upload>
				</b-col>
			</b-row>
		</b-container>
		<div class="FigureListGridContainer" >
			<div class="FigureListGridContainer--inner">

				<figure-list-grid-item v-for="panel in panels" :key="panel.figure_id" :panel="panel" @expanded="handleChildExpansion"></figure-list-grid-item>
			</div>
			
		</div>
		<div v-if="loading" class="loading-indicator">
			<pulse-loader  :loading="loading"  color="gray" class="p-loader"/>
		</div>
	</div>
</template>

<script>
import moment from 'moment'
import { mapGetters } from 'vuex'
import {Bus} from '@/bus'
import commentsAndNotifications from '@/components/notifications/commentsAndNotifications'
import formGetUser from '@/components/user/getUser'
import panelsSummary from '@/components/figures/panelsSummary'
import FigureListGridItem from '@/components/figures/FigureListGridItem'
import newProject from '@/components/projects/newProject'
import Datepicker from 'vuejs-datepicker'
import figureUpload from '@/components/figures/figureUpload'

// https://github.com/greyby/vue-spinner
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import ConfirmButton from '@/components/figures/ConfirmButton.vue'
import userIcon from '@/components/user/userIcon'
import navBar from '@/components/navbar'

export default {
	name: 'FigureListGrid',
	components: { panelsSummary, FigureListGridItem, Datepicker, commentsAndNotifications, formGetUser, PulseLoader, ConfirmButton, userIcon, newProject, figureUpload,navBar },
	props: {
		project: {
			type: Object,
			required: false,
			default: () => ({})
		}
	},
	data () {
		return {
			perPage: 15,
			currentPage: 1,
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
					thClass: 'd-none d-md-table-cell d-lg-table-cell capitalize',
					tdClass: 'd-none d-md-table-cell d-lg-table-cell',
					sortable: true
				},
				{
					key: 'filename',
					label: 'file name',
					sortable: true,
					class: 'breakword'
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
			sortBy: 'modified_date',
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
			isActive: false,
		}
	
	},
	computed: {
		...mapGetters({
			figures: 'figures',
			projects: 'projects',
			user: 'currentUser'
		}),
        panels () {
            let vm = this;
            let panels = [];

            vm.figures.map((figure) => {
                figure.dar.fig.map((panel) => {
                    panels.push({...panel, figure: figure})
                });
             });
             return panels;
        }
	},

	watch: {
	},

	created () {
		var vm = this
		if (vm.$route.params.project_id) {
			vm.filters.project_id = vm.$route.params.project_id
			vm.$store.commit('RESET_FIGURE_FLAGS')
		}
		vm.$store.dispatch('getFigures', { pageNb: vm.pageNb, filters: vm.filters, sortBy: vm.sortBy, sortDesc: vm.sortDesc, limit: vm.limit, resetDisplay: true })
			.then(() => { setTimeout(() => vm.setLoading(false), 300);})
			.catch(function(err){ vm.$snotify.error(err) })

		setTimeout(() => vm.$store.dispatch('getProjects'), 300)		
	},
	mounted () {
		this.scroll()
	},

	methods: {

        setLoading(val) { this.loading = val; },

		handleChildExpansion(obj) {
			Bus.$emit("close-panels", obj);
		},

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
		}


	}
}

</script>

<style scoped>

	.loading-indicator {
		text-align:center;
	}

    .FigureListGridContainer {
        width:100%;
        padding: 10px 40px;
        background-color: #444;
    }

    .FigureListGridContainer--inner {
        display:flex;
        flex-wrap:wrap;
		justify-content: space-between;
        width:100%;
        max-width: 1600px;
        margin:0 auto;
    }

</style>
