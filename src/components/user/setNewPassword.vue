<template>
	<div class="col-md-8 col-md-offset-2">
		<h3>{{siteTitle}} - reset password</h3>
		<div class="card card-default">
			<div class="card card-header"> Set a new password </div>
			<div class = "card-body">
				<form v-on:submit.prevent="setPassword()" name="setNewPasswordForm" id="setNewPasswordForm" >
				
					<div class="form-group row" :class="{ 'has-error': errors.has('password') }">
						<div class="col-3"> <label class="float-right" for="password">Password</label> </div>
						<div class='col-6'><input v-validate='{required:true,min:8,regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/}' type="password" name="password" id="password" class="form-control" v-model="password" ref="password" /></div>
						<div class='col-3'>
							<small v-show="errors.has('password')">
								<span v-if="errors.first('password')=='The password field format is invalid.'">Password must contain at least one numerical, lowercase and uppercase character</span>
								<span v-else>{{ errors.first('password')}}</span>
							</small>
						</div>
					</div>
			
					<div class="form-group row" :class="{ 'has-error': errors.has('password2') }">
						<div class="col-3"> <label class="float-right" for="password2">Confirm password</label> </div>
						<div class='col-6'><input v-validate="'required|confirmed:password'" type="password" name="password2" id="password2" class="form-control" :class="{'is-danger': errors.has('password_confirmation')}" v-model="password2" data-vv-as='password'/></div>
						<div class='col-3'><small v-show="errors.has('password2')">(Password not confirmed)</small></div>
					</div>
				
					<div class="form-group row justify-content-center">
						<div class = 'col-sm-4'>
							<button type="submit" :disabled='errors.any()' class="btn btn-primary">Set password</button>
							<a class="btn btn-link pointer"><router-link :to="{name: 'login'}">Cancel</router-link></a>
						</div>
					</div> 
				</form>
			</div>
		</div>
	</div>	
</template>


<script>
import {HTTP} from '@/router/http';
import {siteTitle} from '@/app_config';
import { mapGetters } from 'vuex';
		
export default {
	name : 'SetNewPassword',
	data() {
		return {
			siteTitle:siteTitle,
			password:'',
			password2:'',
			user:null
		}
	},
	components: {},
	computed:{
		...mapGetters({
			currentUser: 'currentUser'
		})
	},
	methods:{
		setPassword(){
			var vm = this;
			vm.user.password = vm.password;
			vm.user.password2 = vm.password2;
			vm.$validator.validateAll().then(() => {

				HTTP.put('/user',vm.user).then(()=>{
					vm.$snotify.success('Password updated successfully');
					this.$router.push('/');
				});
			});

		},
	},
	mounted() {
		HTTP.get('/user/'+this.currentUser.user_id).then(response => {
			this.user = response.data;
		});
	}
}
</script>
	
<!--scoped style only apply to this component -->
<style scoped>

</style>
