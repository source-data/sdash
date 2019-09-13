<template>
	<div>
		<div class = 'alert text-center' :class = 'message.type' >{{ message.text }}</div>
		<div class="login-link text-center" v-if="showLink"><a href="/login">Log in to SDash</a></div>
	</div>
</template>

<script>

import {HTTP} from '@/router/http';
import Base64 from '@/mixins/base64';

export default{
	name: 'AccountActivation',
	data (){
		return {
			message: {type: '',text: ''},
			showLink: false,
		}
	},
	created () {
		var vm = this;
		var params = Base64.decode(vm.$route.params.param).split(":");
		var userdata = {
			token : params[1]			
		};

		HTTP.put('user/' + params[0] + '/verify', userdata)
			.then(function(res){
				vm.message.type = 'alert-success';
				vm.message.text = 'The account for '+res.data.firstname+' '+res.data.lastname+' has been activated successfully';	
				vm.showLink = true;
			})
			.catch(function(err){
				vm.message.type = 'alert-warning';
				vm.message.text = err;
				vm.showLink = false;
			});
	}
}

</script>