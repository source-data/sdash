<i18n>
{
	"en": {
		"patientname": "Patient name",
		"patientbirthdate": "Birth date",
		"patientid": "Patient ID",
		"patientsex": "Patient sex",
		"modalitiesinpanel": "Modalities in panel",
		"paneldate": "Figure date",
		"panelid": "Figure ID",
		"paneltime": "Figure time",
		"patientinfo": "Patient details",
		"panelinfo": "Figure details",
		"NumberOfFigureRelatedInstances": "Number of instances",
		"NumberOfFigureRelatedPanels": "Number of panels"
	},
	"fr": {
		"patientname": "Nom de patient",
		"patientbirthdate": "Année de naissance",
		"patientid": "ID patient",
		"patientsex": "Sexe du patient",
		"modalitiesinpanel": "Modalité d'étude",
		"paneldate": "Date de l'étude",
		"panelid": "ID étude",
		"paneltime": "Temps d'étude",
		"patientinfo": "Informations du patient",
		"panelinfo": "Information de l'étude",
		"NumberOfFigureRelatedInstances": "Nombre d'instances",
		"NumberOfFigureRelatedPanels": "Nombre de séries"
	}
}
</i18n>

<template>
  <div class="panelMetadataContainer">
    <div class="row">
      <div class="col-xl-1" />
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5 mb-3">
        <h5>{{ $t('patientinfo') }}</h5>
        <table class="table table-striped">
          <tbody>
            <tr v-if="metadata.PatientName">
              <th>{{ $t('patientname') }}</th>
              <td>{{ metadata.PatientName }}</td>
            </tr>
            <tr v-if="matchNumbers(metadata.PatientBirthDate)">
              <th>{{ $t('patientbirthdate') }}</th>
              <td>{{ getDate(metadata.PatientBirthDate[0]) }}</td>
            </tr>
            <tr v-if="metadata.PatientID">
              <th>{{ $t('patientid') }}</th>
              <td>{{ metadata.PatientID[0] }}</td>
            </tr>
            <tr v-if="matchSex(metadata.PatientSex[0])">
              <th>{{ $t('patientsex') }}</th>
              <td>{{ metadata.PatientSex[0] }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5 mb-3">
        <h5>{{ $t('panelinfo') }}</h5>
        <table class="table table-striped">
          <tbody>
            <tr v-if="metadata.ModalitiesInFigure">
              <th>{{ $t('modalitiesinpanel') }}</th>
              <td>{{ metadata.ModalitiesInFigure[0] }}</td>
            </tr>
            <tr v-if="matchNumbers(metadata.FigureDate)">
              <th>{{ $t('paneldate') }}</th>
              <td>{{ metadata.FigureDate[0]|formatDate }}</td>
            </tr>
            <tr v-if="metadata.FigureID">
              <th>{{ $t('panelid') }}</th>
              <td>{{ metadata.FigureID[0] }}</td>
            </tr>
            <tr v-if="matchNumbers(metadata.FigureTime)">
              <th>{{ $t('paneltime') }}</th>
              <td>{{ metadata.FigureTime[0] }}</td>
            </tr>
            <tr v-if="matchNumbers(metadata.NumberOfFigureRelatedPanels)">
              <th>{{ $t('NumberOfFigureRelatedPanels') }}</th>
              <td>{{ metadata.NumberOfFigureRelatedPanels[0] }}</td>
            </tr>
            <tr v-if="matchNumbers(metadata.NumberOfFigureRelatedInstances)">
              <th>{{ $t('NumberOfFigureRelatedInstances') }}</th>
              <td>{{ metadata.NumberOfFigureRelatedInstances[0] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'figureMetadata',
	props: {
		id: {
			type: String,
			required: true
		}
	},
	data () {
		return {}
	},
	computed: {
		...mapGetters({
			panels: 'panels',
			user: 'currentUser'
		}),
		metadata () {
			let panelIdx = _.findIndex(this.panels, s => {
				return s.FigureInstanceUID[0] === this.id
			})
			if (panelIdx > -1) {
				return this.panels[panelIdx]
			}
			return {}
		}
	},
	methods: {
		getDate (date) {
			var year = date.substr(0, 4)
			var month = date.substr(4, 2)
			var day = date.substr(6, 2)
			return day + '/' + month + '/' + year
		},
		matchSex (sex) {
			return /m|M|o|O|f|F/.test(sex)
		},
		matchNumbers (number) {
			return /^[0-9]*([,.][0-9]*)?$/.test(number)
		}
	}
}

</script>

<style scoped>
div.description {
	width: 290px;
	padding: 0 20px;
	float: left;
}
label {
	font-size: 130%;
}
</style>
