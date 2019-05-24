<i18n>{
	"en": {
		"numberimages": "Number of images",
		"caption": "caption",
		"label": "label",
		"openviewer": "smart-figure"
	},
	"fr": {
		"numberimages": "Nombre d'images",
		"caption": "légende",
		"label": "numéro",
		"openviewer": "smart-figure"
	}
}
</i18n>

<template>
  <div class="panelsSummaryContainer">
    <div class="row justify-content-center">
      <div>
        <img class="cursor-img" :src="panel.graphic.imgsrc" width="250" @click="openViewer">
				<p v-if = "supplementaryMaterial.length"><b-badge pill variant="secondary" v-for="(supp,idx) in supplementaryMaterial" :key="idx" class="badge" :title="supp.href" v-b-tooltip.hover>{{supp|mimeExtension}}</b-badge></p>
      </div>
      <div class="col col-mb-2 col-sm-10 col-md-8 col-lg-6 description">
        <table class="table table-striped table-sm">
          <tbody>
            <tr v-if="panel.label">
              <th>{{ $t('label') }}</th>
              <td>{{ panel.label }}</td>
            </tr>
            <tr v-if="panel.caption">
              <th>{{ $t('caption') }}</th>
              <td class="tdcaption"><div class = "caption" v-html="panel.caption"></div><div class = 'backgroundcaption'></div></td>
            </tr>
						<tr v-for="(kwd,label) in kwdGroups" :key="label">
							<th>{{label}}</th>
							<td v-html="kwd"></td>
						</tr>
          </tbody>
        </table>

        <dl class="row justify-content-center">
          <dd>
            <button type="button" class="btn-primary btn-sm"  @click="openViewer">{{ $t('openviewer') }}</button>
          </dd>
        </dl>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'panelsSummary',
	props: {
		panelID: {
			type: Number,
			required: true
		},
		figureID: {
			type: Number,
			required: true
		}
	},
	data () {
		return {}
	},
	computed: {
		...mapGetters({
			figures: 'figures',
			user: 'currentUser'
		}),
		panel () {
			let figureIndex = _.findIndex(this.figures, s => { return s.id === this.figureID })

			if (figureIndex > -1) {
				let panelIndex = _.findIndex(this.figures[figureIndex].dar.fig, d => { return d.id === this.panelID })
				if (panelIndex > -1){
					return this.figures[figureIndex].dar.fig[panelIndex]
				} 
			}

			return {}
		},
		kwdGroups () {
			let kwdGroups = {};
			_.forEach(this.panel['kwd-group'], k => {
				if (!k.kwd) return
				if (kwdGroups[k.label] === undefined){
					kwdGroups[k.label] = k.kwd
				} 
				else kwdGroups[k.label] += ", "+k.kwd
			})
			return kwdGroups
		},
		supplementaryMaterial () {
			let mat = [];
			if (this.panel['supplementary-material'] === undefined) {
				return mat
			} 
			return this.panel['supplementary-material']
		}
	},
	methods: {
		openViewer () {
			this.$snotify.info("Opening the Smart-Figure")
		}
	}
}

</script>

<style scoped>
div.panelsSummaryContainer{
	font-size: 90%;
	line-height: 1.5em;
}
label{
	font-size: 130%;
}
.cursor-img{
	cursor: pointer;
}

table td div.caption {
	max-height: 200px;
	overflow-y: auto;
	padding-bottom: 30px;
}

table td.tdcaption{
	position: relative;	
}

table td.tdcaption div.backgroundcaption { 
	position: absolute;
	bottom: 0;
	width: 100%;
	height: 50px;
	/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#ffffff+0,ffffff+100&0+0,1+100 */
	background: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
}
</style>
