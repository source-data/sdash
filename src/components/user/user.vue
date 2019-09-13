<template>
	<div class="user_container">
		<div class = 'container '>
			<h3 style="margin-bottom:20px">Edit user</h3>
			<div class="card">
				<div class="card-header mb-3"><h6 class="float-left">{{user.firstname}} {{user.lastname}}</h6></div>
				<div class="card-block">
					<form name="updateUserForm" id="updateUserForm" v-on:submit.prevent="updateUser()">

						<div class="form-group row" >
							<div class="col-3 col-form-label mr-2"> <label class="float-right" for="username">Login name</label> </div>
							<div class="col-6"><p class="float-left mt-2">{{user.login}}</p></div>
						</div>

						<div class="form-group row" v-bind:class="{ 'has-error': errors.has('firstname') }">
							<div class="col-3"> <label class="float-right" for="firstname">First name</label> </div>
							<div class='col-6'><input v-validate="'required'" type="text" name="firstname" id="firstname" class="form-control" v-model="user.firstname" /></div>
							<div class='col-3'><small v-show="errors.has('firstname')">{{ errors.first('firstname') }}</small></div>
						</div>

						<div class="form-group row" v-bind:class="{ 'has-error': errors.has('lastname') }">
							<div class="col-3"> <label class="float-right" for="lastname">Last name</label> </div>
							<div class='col-6'><input v-validate="'required'" type="text" name="lastname" id="lastname" class="form-control" v-model="user.lastname"/></div>
							<div class='col-3'><small v-show="errors.has('lastname')"> {{ errors.first('lastname') }}</small></div>
						</div>

						<div class="form-group row" v-bind:class="{ 'has-error': errors.has('email') }">
							<div class="col-3"> <label class="float-right" for="email">Email</label> </div>
							<div class='col-6'><input v-validate="'required|email'" type="email" name="email" id="email" class="form-control" v-model="user.email"/></div>
							<div class='col-3'><small v-show="errors.has('email')"> {{ errors.first('email') }}</small></div>
						</div>

						<div class="form-group row" v-if="user.user_id != currentUser.user_id" v-access="'admin'">
							<div class="col-3"> <label class="float-right" for="password2">Password</label> </div>
							<div class='col-9'><button class="btn btn-info" type="button" @click="resetPassword()">Reset password</button></div>
						</div>

						<div v-if="user.user_id == currentUser.user_id">
							<div class="form-group row" v-bind:class="{ 'has-error': errors.has('password') }">
								<div class="col-3"> <label class="float-right" for="password">Password</label> </div>
								<div class='col-6'><input v-validate='{required:false,min:8,regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/}' type="password" name="password" id="password" class="form-control" ref="password" v-model="user.password"/></div>
								<div class='col-3'>
									<small v-show="errors.has('password')">
										<span v-if="errors.first('password')=='The password field format is invalid.'">Password must contain at least one numerical, lowercase and uppercase character</span>
										<span v-else>{{ errors.first('password')}}</span>
									</small>
								</div>
							</div>

							<div class="form-group row" v-bind:class="{ 'has-error': errors.has('password2') }">
								<div class="col-3"> <label class="float-right" for="password2">Confirm password</label> </div>
								<div class='col-6'><input v-validate="'required|confirmed:password'" type="password" name="password2" id="password2" class="form-control" v-model="user.password2" data-vv-as='password'/></div>
								<div class='col-3'><small v-show="errors.has('password2')">(Password not confirmed)</small></div>
							</div>
						</div>


						<div class="form-group row"  v-access='"admin"'>
							<div class="col-3"> <label class="float-right" for="role">Role</label> </div>
							<div class = 'checkbox col-6'><select v-model='user.role'  class = 'form-control'><option v-for="(cv_role,idx) in cv_roles" :value='cv_role' :key='idx'>{{cv_role}}</option></select></div>
						</div>

						<div class="form-group row" v-access='"admin"'>
							<div class="col-3"> <label class="float-right" for="is_active">Is active</label> </div>
							<div class = 'checkbox col-6'><input class="input-form float-left pointer mt-1" type="checkbox" id="is_active" true-value = 'Y' false-value = 'N' v-model = 'user.is_active'></div>
						</div>


						<div class="form-group row" v-if='user.qr_img_src'>
							<div class="col-3"> <label class="float-right" for="qr_img_src">Google Authenticator QR</label> </div>
							<div class = 'col-6'><img :src='user.qr_img_src'></div>
						</div>


						<div class="form-group row justify-content-center">
							<div class='col-4'>
								<button type="submit" :disabled='errors.any()' class="btn btn-primary">Update account</button>
								<a class="btn btn-link pointer" v-access="'admin'"><router-link :to="{name: 'admin', query: {'nav': 'user'}}">Back to user list</router-link></a>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {HTTP} from '@/router/http';
import {siteTitle} from '@/app_config';
import { mapGetters } from 'vuex';

export default {
	name : 'User',
	data() {
		return {
			siteTitle:siteTitle,
			user:{},
			is_admin:false,
			cv_roles: []
		}
	},
	computed:{
		...mapGetters({
			currentUser: 'currentUser'
		})
	},
	methods:{
		resetPassword:function(){
			var vm = this;
			HTTP.post('/resetpass',{email:vm.user.email}).then( () => {
				vm.$snotify.success('reset password successfully');
			});

		},

		updateUser:function(){
			var vm = this;
			vm.$validator.validateAll().then((result) => {
				if (result){
					vm.user.groups = _.filter(vm.groups,'selected');
					HTTP.put('/user',vm.user)
						.then(response => { vm.user = response.data; vm.$snotify.success('Account updated successfully')})
						.catch(err => { if (!_.isEmpty(err)) vm.$snotify.error(err); });
				}
			});
		}
	},
	mounted() {
		var vm = this;
		vm.is_admin = this.currentUser.permissions.indexOf('admin')>-1;
		var user_id = vm.$route.params.user_id;
		HTTP.get('/user/'+user_id).then(response => {
			vm.user = response.data;
		});

		HTTP.get('/cv_user_roles').then(response => {
			var vm = this;
			vm.cv_roles = response.data;
		});

	}
}
</script>

	<!--scoped style only apply to this component -->
<style scoped>
.user{
	/*		margin:5%;*/
}
.strong{
	font-weight:bold;
}

</style>