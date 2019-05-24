<template>
	<div class = 'container'>
		<h2>Welcome {{user.firstname}}</h2>

		<div class = 'jumbotron'>
			<p>Your account has been created successfully, but has not been validated by {{user.validator.firstname + " " + user.validator.lastname}} ({{user.validator.email}});</p>
			<p>You will receive an email once this is done.</p>
		</div>

	</div>
</template>

<script>
import {mapGetters} from 'vuex';
export default{
	name: 'AccountValidation',
	computed: {
		...mapGetters({
			user: 'currentUser'
		})
	},
	mounted () {
		if (!this.user.user_id) {
			this.$router.push('/login');
		}
	}
}
</script>