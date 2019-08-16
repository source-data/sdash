<template>
	<div class = 'container'>
		<h2>Welcome {{user.firstname}}</h2>

		<div class = 'jumbotron'>
			<p>Please confirm that you are the owner of the registered email address by clicking the link in the registration email.</p>
			<p>Once this is done, you will have access to SDash.</p>
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