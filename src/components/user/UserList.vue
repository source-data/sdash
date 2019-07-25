<template>
	<div class="userList_container">
		<div class = 'container-fluid'>

			<h3 style="margin-bottom:20px" v-if="!from">List of users<router-link v-access='"admin"' class="float-right btn btn-sm btn-primary" to="/groups">manage groups</router-link></h3>

			<div class="card">
				<div class="card-header mb-3"><h6 class="float-left mt-1">Users</h6></div>
				<div class="card-block">
					<div class="row">
						<div class="col-sm-4 mb-2 ml-2">
							<input type="text" name="userlist_filter" id="userlist_filter" v-model="table_filter" class="form-control" placeholder="Global search..." />
						</div>
					</div>
					
					<table class="table">
						<thead>
							<tr>
								<th class="pointer" v-on:click="sortBy('lastname')"> User <sort-icon :sorter='table_sorter' ccas='lastname'></sort-icon> </th>
								<th class="pointer" v-on:click="sortBy('email')"> Email <sort-icon :sorter='table_sorter' ccas='email'></sort-icon> </th>
								<th class="pointer" v-on:click="sortBy('role')"> Role <sort-icon :sorter='table_sorter' ccas='role'></sort-icon> </th>
								<th>Active</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="user in filteredUsers" :key='user.user_id'>
								<td><router-link :to="{name: 'user', params: {user_id: user.user_id}}"><span class="uppercase">{{user.lastname}}</span> {{user.firstname}}</router-link></td>
								<td><a v-bind:href=" `mailto:${user.email}` ">{{user.email}}</a></td>
								<td>{{user.role}}</td>
								<td><v-icon v-if="user.is_active=='N'" name="ban" style="fill:red"></v-icon><v-icon v-else name="check-circle" style="fill:green"></v-icon></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>	
	</div>	
</template>

<script>
import {HTTP} from '@/router/http';
import SortIcon from '@/components/globals/sortIcon'
		
export default {
	name : 'UserList',
	data() {
		return {
			users:[],
			table_filter:null,
			table_sorter:{cas:'name',order:'asc'},
			filtered_fields:['firstname','lastname','email','role']
		}
	},
	props:['from'],
	components: {
		'sort-icon': SortIcon			
	},
	computed:{
		filteredUsers(){
			var vm = this;
			var f_users = [];
			f_users = _.filter(vm.users,function(u){
				if (!vm.table_filter){ return true; }
				else{
					var pass = false;
					_.forEach(vm.filtered_fields,function(f){
						if (u[f].toLowerCase().indexOf(vm.table_filter.toLowerCase())>-1) pass = true;
					});
					return pass;
				}
			});
			f_users = _.orderBy(f_users,vm.table_sorter.cas,vm.table_sorter.order);
			return f_users;
		}
	},
	methods:{
		setUsers(users){
			this.users = _.values(Object.assign({}, this.users, users));
		},
		sortBy(cas){
			var vm = this;
			vm.table_sorter.cas = cas;
			vm.table_sorter.order = (cas==vm.table_sorter.cas && vm.table_sorter.order =='asc') ? 'desc' : 'asc';
		}
	},

	beforeRouteEnter(to, from, next) {
		HTTP.get('/users').then(res => {
			next(vm => vm.setUsers(res.data));
		});
	},
	mounted() {
		HTTP.get('/users').then(res => {
			this.users = res.data;
		});
	}
}
</script>
	
<!--scoped style only apply to this component -->
<style scoped>
.userList_container{
	margin:3% 1%;
}
</style>
	