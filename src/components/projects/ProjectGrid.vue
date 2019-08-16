<template>
  <div>
    <div class="container">
      <div  v-if="formattedProjectDescription[0] !== ''"  class="card project-description">
        <div class="card-body">
          <p  v-for="(p,idx) in formattedProjectDescription"  :key="idx"  class="py-0 my-0"  :class="(idx)?'pl-3':''"> {{ p }} </p>
        </div>
      </div>
      <div class="card my-3">
        <div class="card-body user-info-cards">
			<div v-for="user in project.users"  :key="user.user_id"  class="py-0 user-info-container">
			<b-badge pill class="admin-pill" v-if="user.is_admin">Admin</b-badge>
			<div class="user-info-container--avatar-container">
				<user-icon :name="user.name" v-if="!user.profile_picture_filename"></user-icon>
				<img v-if="user.profile_picture_filename" class="user-info-container--avatar" :src="user.profile_picture" :alt="user.name">
			</div>
			<div class="user-info-container--name-container">
				{{user.name}}
			</div>
			<div class="user-info-container--institution-container">
				{{user.institution_name}}
			</div>
			</div>
        </div>
      </div>
			<h6 v-if="project.url" class = "mt-3"><b>URL: </b><a :href="project.url" target="_blank" class="pl-1">{{project.url}}</a></h6>
    </div>
    <figure-list-grid :project="project" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import FigureListGrid from '@/components/figures/FigureListGrid.vue'
import userIcon from '@/components/user/userIcon'

export default {
	name: 'projectGrid',
	components: { FigureListGrid, userIcon },
	data () {
		return {

		}
	},
	computed: {
		...mapGetters({
			project: 'project'
		}),
		formattedProjectDescription () {
			return this.project.description.split('\n')
		}
	}
}

</script>

<style scoped>
  .project-description {
      margin-bottom: 2rem;
  }

	.user-info-cards {
		display:flex;
		text-align:center;
		flex-wrap:wrap;
	}

	.user-info-container {
		width: 140px;
		margin-right: 16px;
		margin-bottom: 16px;
		position: relative;

	}

	.user-info-container--avatar-container {
		width: 80px;
		height:80px;
		display: flex;
		justify-content: center;
		align-items: center;
		margin: 0 auto;
	}

	.user-info-container--avatar {
		width:100%;
		border-radius: 50%;
	}

	.user-info-container--name-container {
		color: #be6be6;
		font-weight: bold;
	}

	.user-info-container--institution-container {
		color: #3e3e3e;
		font-size:0.96em;
	}

	.admin-pill {
		position: absolute;
		top:0;
		right: 16px;
		background-color: #2eb92e;
	}
</style>
