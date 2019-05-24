<i18n>
{
	"en" : {
		"inbox": "my files",
		"projects": "projects",
		"favorites": "favorites",
		"recents": "recents"
	},
	"fr": {
		"inbox": "mes fichiers",
		"projects": "projets",
		"favorites": "favoris",
		"recents": "récents"
	}
}
</i18n>

<template>
  <div id="navbar_container" class="container">
    <div class="nav-container">
      <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
          <router-link to="/figures" class="nav-link" active-class="active" v-access = "'active'">
            <v-icon name="bars" />{{ $t("inbox") }}
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/projects" class="nav-link" active-class="active" v-access = "'active'">
            <v-icon name="images" />{{ $t("projects") }}
          </router-link>
        </li>
        <!--
				<li class="nav-item">
					<router-link to="/favorites" class="nav-link" active-class="active"><v-icon name='star'/>{{ $t("favorites") }}</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/recents" class="nav-link" active-class="active"><v-icon name='clock'/>{{ $t("recents") }}</router-link>
				</li>
				-->
      </ul>
    </div>
  </div>
</template>

<script>
export default {
	name: 'NavBar',
	data () {
		return {
			activePath: 'inbox'
		}
	},
	watch: {
		'$route' (to) {
			this.activePath = to.path.split('/')[1]
		}
	}
}

</script>

<style scoped>
#navbar_container{
/*	margin-top: 125px;*/
}

li.nav-item {
	text-transform: capitalize;
}

svg.fa-icon{
	margin-right: 10px;
}
</style>
