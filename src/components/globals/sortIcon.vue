<template>
	<span>
		<v-icon v-if="sorter.cas!=ccas" class="ml-1 pt-1" name='sort'></v-icon>
		<v-icon v-if="sorter.cas==ccas && sorter.order=='asc'" class="ml-1 pt-1" name='sort-up'></v-icon>
		<v-icon v-if="sorter.cas==ccas && sorter.order=='desc'" class="ml-1 pt-1" name='sort-down'></v-icon>
	</span>
</template>

<script>
	
export default {
	name: 'SortIcon',
	props: ['sorter','ccas']
}

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
