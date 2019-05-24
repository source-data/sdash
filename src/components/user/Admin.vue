<template>
	<div class="admin_container">
		<div class="container">
			<h4 class="text-center">Admin interface of {{siteTitle}}</h4>

			<ul class="nav nav-tabs" style="margin-top:20px">
				<li class="pointer nav-item" v-on:click="changeNav('user')"><a class="nav-link" :class="(nav=='user') ? 'active':''">Users</a> </li>
			</ul>

			<div v-if="nav=='user'"><user-list from='admin'></user-list></div>
			<div v-if="nav=='group'"><group-list from='admin'></group-list></div>


		</div>
	</div>
</template>

<script>
import {siteTitle} from '@/app_config'
import UserList from '@/components/user/UserList'

export default {
	name: 'admin',
	components: {
		'user-list':UserList
	},
	data () {
		return {
			siteTitle:siteTitle,
			nav:'user'
		}
	},
	methods:{
		changeNav(cas){
			this.nav = cas;
		}
	},
	mounted(){
		var vm = this;
		if (vm.$route.query.nav && _.includes(['user'],vm.$route.query.nav)) vm.nav = vm.$route.query.nav;
	}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.admin_container{
	margin-top:100px !important;
}
form{
	margin:20px 0;
}
.card{
	margin:20px 0;
}
</style>











