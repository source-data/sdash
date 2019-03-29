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
