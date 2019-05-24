<template>
  <div id="app">
    <vue-snotify />
    <nav-header />
    <!-- <nav-bar /> -->
		
    <!-- content -->
    <router-view style="margin-top: 100px" />

    <!-- footer -->
    <footer>
      <b-navbar type="info" navbar-dark bg-primary fixed="bottom" >
        <b-navbar-brand href="#" tag="h4">@ SIB Swiss Institute of Bioinformatics and EMBO, inc {{ year }}
        </b-navbar-brand>
      </b-navbar>
    </footer>
  </div>
</template>

<script>

import navHeader from '@/components/navheader'

export default {
	name: 'App',
	components: { navHeader },
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

