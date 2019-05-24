<template>
	<div class="container">
		<h3>{{siteTitle}}</h3>
		<div class="card">
			<div class="card-header">
				<div class="card-title">Reset password</div>
			</div>
			<div class = "card-body">
				<form @submit.prevent = 'resetPassword()' novalidate name = 'form' role = 'form'>
					<div class = 'form-group row'>
						<label for="email" class = 'col-sm-2 control-label'>Email</label>
						<div class = 'col-sm-5'>
							<input type="email"  id = 'email' name = 'email' required v-model = "email" class = 'form-control'>
						</div>
						<div class = 'col-sm-5'>
							<button type = 'submit' class = 'btn btn-primary'>Reset password</button>
							<router-link to = '/login' class = 'btn btn-link'>{{$t('cancel')}}</router-link>
						</div>
					</div>
				</form>
			</div>
			<div class = 'card-footer'>
				<p><small>A new temporary password will be sent to this email address.</small></p>
			</div>
		</div>
	</div>

</template>

<script>

import {HTTP} from '@/router/http'
import {siteTitle} from '@/app_config'


export default{
	data () {
		return {
			email: '',
			siteTitle: ''
		}
	},
	methods:{
		resetPassword() {
			var vm = this;
			HTTP.post('resetpass',{email: vm.email}).then( () => {
				vm.$snotify.success('An email has been sent to: '+vm.email,"Password reset successfully");
				vm.$router.push('/login');
			}).catch(err => {
				if (!_.isEmpty(err)) vm.$snotify.error(err);
			});

		}
	},
	created () {
		this.siteTitle = siteTitle;
	}
}

</script>