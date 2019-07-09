<template>
	<div class="container">
		<h3>Welcome to the {{siteTitle}} platform</h3>
		<div class = 'card ' style = 'margin-top: 50px;'>
			<div class = 'card-header'>
				<div class = 'card-title'>Registration</div>
			</div>
			<div class = 'card-body'>
				<form name="register_form" @submit.prevent="register" role="form">

					<div class="form-group row" :class="{'has-error': errors.has('login') }">
						<div class="col-sm-3"><label class='float-right' for="login">Login name</label></div>
						<div class='col-sm-6'> <input type="text" name="login" id="login" class="form-control" v-model="user.login" v-validate="'required'"> </div>
						<div class='col-sm-3'> <small v-if='errors.has("login")'>{{ errors.first('login') }}</small></div>
					</div>

					<div class="form-group row" v-bind:class="{ 'has-error': errors.has('firstname') }">
						<div class="col-sm-3"><label class="float-right" for="firstname">First name</label> </div>
						<div class='col-sm-6'><input v-validate="'required|alpha_dash'" type="text" name="firstname" id="firstname" class="form-control" v-model="user.firstname" /></div>
						<div class='col-sm-3'><small v-show="errors.has('firstname')">{{ errors.first('firstname') }}</small></div>
					</div>

					<div class="form-group row" :class="{'has-error': errors.has('lastname') }">
						<div class="col-sm-3"><label class="float-right" for="lastname">Last name</label> </div>
						<div class='col-sm-6'><input type="text" name="lastname" id="lastname" class="form-control" v-model="user.lastname" v-validate="'required'"/> </div>
						<div class='col-sm-3'><small v-if='errors.has("lastname")'>{{ errors.first('lastname') }}</small></div>
					</div>

					<div class="form-group row" :class="{'has-error': errors.has('email') }">
						<div class="col-sm-3"> <label class="float-right" for="email">Email</label> </div>
						<div class='col-sm-6'> <input type="text" name="email" id="email" class="form-control" v-model="user.email" v-validate='"required|email"'/> </div>
						<div class='col-sm-3'><small v-if='errors.has("email")'>{{ errors.first('email') }}</small></div>
					</div>


					<div class="form-group row" :class="{'has-error': errors.has('password') }">
						<label class='col-sm-3 col-form-label text-right' for="password">Password</label>
						
						<div class='col-sm-6'><input type="password" name="password" id="password" class="form-control" v-model="user.password" v-validate='{required:true,min:8,regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/}' ref="password"/></div>
						<div class='col-sm-3' v-if='errors.has("password")'>
							<small v-if="errors.first('password')=='The password field format is invalid.'">Password must contain at least one numerical, lowercase and uppercase character</small>
							<small v-else>{{ errors.first('password')}}</small>
						</div>
					</div>

					<div class="form-group row" :class="{'has-error': errors.has('password2') }">
						<div class="col-sm-3"><label class='float-right' for="password2" >Confirm password</label></div>
						<div class='col-sm-6'><input type="password" name="password2" id="password2" class="form-control" v-model="user.password2"  v-validate="'required|confirmed:password'" data-vv-as='password'/></div>
						<div class='col-sm-3' v-if='errors.has("password2")'><small>{{ errors.first('password2') }}</small></div>
					</div>
					<div class="form-group row">
						<div class='col-sm-6 offset-3'>
							<button type="submit" class="btn btn-primary" :disabled='!user.login||errors.any()'>Register</button>
							<router-link to="/login" class="btn btn-link">Cancel</router-link>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</template>


<script>
import Vue from 'vue';
import VeeValidate from 'vee-validate';
import {siteTitle} from '@/app_config';

Vue.use(VeeValidate, {fieldsBagName: 'formFields'});

export default {
	name: 'register',
	data (){
		return {
			user: {
				login: '',
				firstname: '',
				lastname: '',
				email: '',
				password: '',
				password2: ''
			},
			siteTitle: siteTitle
		}
	},
	methods: {
		register () {
			var vm = this;
			this.$validator.validateAll().then((result) => {
				if (result){
					vm.$store.dispatch('register',vm.user).then(() => {
						vm.$snotify.success('Account created successfully');
						vm.$router.push('/validationrequired')

					}).catch((err) => {
						if (!_.isEmpty(err)) vm.$snotify.error(err);
					});
				}
			});
		}
	}
}
</script>

<style scoped>
.error_warning{
	float:left;
	margin-top:5px;
	font-style:italic;
	font-size:11px;
	color:red;
}
</style>