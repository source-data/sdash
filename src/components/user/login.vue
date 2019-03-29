<i18n>
{
	"en": {
		"firstname": "firstname",
		"lastname": "lastname"
	},
	"fr": {
		"firstname": "prénom",
		"lastname": "nom"
	}
}
</i18n>

<template>
	<div class = "login container">
		<b-card header = 'Login'>
			<form @submit.prevent="login">
				<dl class = 'row'>
					<dt class = 'col-3'>{{$t('firstname')}}</dt>
					<dd class = 'col-6'><input type = 'text' placeholder='...' v-validate="'required'" class="form-control" v-model="user.firstname" name="firstname"/></dd>
					<div class = 'col-3'><span>{{ errors.first('firstname') }}</span></div>
				</dl>
				<dl class = 'row'>
					<dt class = 'col-3'>{{$t('lastname')}}</dt>
					<dd class = 'col-6'><input type = 'text' placeholder='...' v-validate="'required'" class="form-control" v-model="user.lastname" name = "lastname"/></dd>
					<div class = "col-3"><span>{{ errors.first('lastname') }}</span></div>
				</dl>
				<div class = 'offset-3'><button type = 'submit' class = 'btn btn-primary' :disabled="!isFormValid">login</button></div>
			</form>
		</b-card>
	</div>
</template>

<script>
export default {
	name: "login",
	data () {
		return {
			user: {
				firstname: 'Robin',
				lastname: 'Liechti'
			}			
		}
	},
	created () {
		this.$store.commit("LOGOUT");
	},
	computed: {
		isFormValid () {
			return this.user.firstname && this.user.lastname
		}
	},
	methods: {
		login () {
			this.$store.dispatch('login',this.user).then(user => {
				if (user) this.$router.push("/figures")
			})
		}
	}
}
</script>

<style scoped>

dt{
	text-align: right;
}

</style>