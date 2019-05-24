<template>
	<div class = 'alert text-center' :class = 'message.type' >{{ message.text }}</div>
</template>

<script>

import {HTTP} from '@/router/http';
import Base64 from '@/mixins/base64';

export default{
	name: 'AccountActivation',
	data (){
		return {
			message: {type: '',text: ''}
		}
	},
	created () {
		var vm = this;
		var params = Base64.decode(vm.$route.params.param).split(":");
		var user_id = +params[0];
		var code = params[1];
		var validator_login = params[2];
		var validator_jwt = params[3];

		if (validator_login && validator_jwt) {
			HTTP.defaults.headers.common['Authorization'] = 'Bearer '+validator_jwt;
		}

		HTTP.get('user/'+user_id).then(function(res){
			var user = res.data;
			if (user_id !== user.user_id || code !== user.activation_code || validator_login !== user.validator.login){
				vm.message.type = 'alert-danger';
				vm.message.text = 'Sorry, activation code is not valid';
			} else if(user.is_active == 'Y'){
				vm.message.type = 'alert-warning';
				vm.message.text = 'User '+user.firstname+' '+user.lastname+' is already active';
			} else if (vm.$route.path.indexOf('activation') > -1){
				user.is_active = 'Y';
				HTTP.put('user',user).then(function(){
					vm.message.type = 'alert-success';
					vm.message.text = 'The account for '+user.firstname+' '+user.lastname+' has been activated successfully';
				});
			} else if(vm.$route.path.indexOf('reject') > -1){
				user.is_active = 'R';
				HTTP.put('user',user).then(function(){
					vm.message.type = 'alert-warning';
					vm.message.text = 'The account for '+user.firstname+' '+user.lastname+' has been rejected successfully. An email has been sent to '+user.email+'.';
				});
			}
		});
	}
}

</script>