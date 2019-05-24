<i18n>
{
	"en": {
		"welcome": "Welcome to",
		"username": "username",
		"password": "password",
		"submit":"Login",
		"register":"Register",
		"reset":"Reset password"
	},
	"fr": {
		"welcome": "Bienvenue sur",
		"username": "login",
		"password": "mot de passe",
		"submit":"Login",
		"register":"Créer un compte",
		"reset":"Mot de passe oublié ?"

	}
}
</i18n>

<template>
	<div class="login_container">
		<div class="container">
			<h4>{{$t('welcome')}} {{siteTitle}}</h4>

			<div class="card" v-if='!show_ga'>
				<div class="card-header"> Login </div>
				<div class="card-block">

					<form v-on:submit.prevent="loginUser()">
						<div class="form-group row" :class="{ 'has-error': errors.has('username') }">
							<div class="col-3"><label class="float-right" for="username">{{$t('username')}}</label> </div>
							<div class="col-6"> <input type="text" v-validate="'required'" name="username" id="username" v-model="user.username" class="form-control" autofocus></div>
							<div class='col-3'><small v-show="errors.has('username')"> {{ errors.first('username') }}</small></div>
						</div>
						<div class="form-group row" :class="{ 'has-error': errors.has('password') }">
							<div class="col-3"> <label class="float-right" for="username">{{$t('password')}}</label> </div>
							<div class="col-6"><input type="password" v-validate="'required'" name="password" id="password" v-model="user.password" class="form-control"></div>
							<div class='col-3'><small v-show="errors.has('password')"> {{ errors.first('password') }}</small></div>
						</div>

						<div class="form-group row justify-content-center">
							<button type="submit" class="btn btn-primary" :disabled='errors.any()'>{{$t('submit')}}</button>
							<router-link to="/register" class="btn btn-link">{{$t('register')}}</router-link>
							<router-link to="/forgetPassword" class="btn btn-link">{{$t('reset')}}</router-link>
						</div>
					</form>
				</div>
			</div>
			
	</div>
</div>
</template>

<script>
import {siteTitle} from '@/app_config';
import {Bus} from '@/bus'


export default {
	name: 'login',
	data () {
		return {
			siteTitle:siteTitle,
			user:{username:'',password:'',ga_code: '',qr_img_src: '',user_id: '',ga_status: ''},
			show_ga: false
		}
	},

	methods:{
		loginUser () {
			var vm = this;
			vm.$validator.validateAll().then((result) => {
				if (result){
					vm.$store.dispatch('login',vm.user).then((user) => {
						vm.$snotify.success('logged in successfully');
						Bus.$emit('user.updated',true);
						var nextPage = (user.is_password_reset=='Y') ? 'setnewpassword' : (vm.$route.query.redirect) ? vm.$route.query.redirect : '/';
						vm.$router.push(nextPage);								
					}).catch((err) => {
						if (!_.isEmpty(err)) vm.$snotify.error(err);
					});
				}
			})
		},
				
	},
	created (){
		this.$store.dispatch('logout');
		Bus.$emit('user.updated',true);
	}
}
</script>


<style scoped>
	.login_container{
		margin-top:100px;
	}
	form{
		margin:20px 0;
	}
	.card{
		margin:20px 0;
	}
	
dt{
	text-align: right;
}

</style>