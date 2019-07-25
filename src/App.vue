<template>
  <div id="app">
    <vue-snotify />
    <nav-header />
		<account-validation></account-validation>
    <!-- <nav-bar /> -->
		
    <!-- content -->
    <router-view style="margin-top: 100px" />

    <!-- footer -->
    <footer>
		<b-navbar toggleable="md" type="light" variant="light" fixed='bottom'>
			<b-navbar-brand href="https://www.vital-it.ch/"  tag='h4' class="mr-auto">© SIB Swiss Institute of Bioinformatics / Vital-IT {{year}}
			</b-navbar-brand>
			<span class = 'imi-logo'></span>
		</b-navbar>
    </footer>
  </div>
</template>

<script>

import navHeader from '@/components/navheader'
import accountValidation from '@/components/user/Validation'
	
export default {
	name: 'App',
	components: { navHeader,accountValidation },
	computed: {
		year () {
			return new Date().getFullYear()
		}
	}
}
</script>

<style>

		
#app{
	padding-bottom: 50px;
}
.pointer{
	cursor:pointer;
}
.ban{
	cursor:not-allowed;
}

.uppercase{
	text-transform:uppercase;
}

a.navbar-brand {
	color: #CCC;
	font-size: 11pt;
}
footer nav{
	width: 100%;
	background-color: #FFF;
}

.capitalize { 
	text-transform: capitalize;
}


</style>

