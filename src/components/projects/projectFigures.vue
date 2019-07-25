<i18n>
{
}
</i18n>

<template>
  <div>
    <div class="container">
      <div  v-if="formattedProjectDescription[0] !== ''"  class="card">
        <div class="card-body">
          <p  v-for="(p,idx) in formattedProjectDescription"  :key="idx"  class="py-0 my-0"  :class="(idx)?'pl-3':''"> {{ p }} </p>
        </div>
      </div>
			<h6 v-if="project.url" class = "mt-3"><b>URL: </b><a :href="project.url" target="_blank" class="pl-1">{{project.url}}</a></h6>
    </div>
    <figures :project="project" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Figures from '@/components/figures/list.vue'

export default {
	name: 'projectFigures',
	components: { Figures },
	data () {
		return {

		}
	},
	computed: {
		...mapGetters({
			project: 'project'
		}),
		formattedProjectDescription () {
			return this.project.description.split('\n')
		}
	}
}

</script>

<style scoped>

</style>
