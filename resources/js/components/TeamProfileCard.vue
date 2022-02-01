<template>
  <article class="sd-team-profile-card">
    <header class="sd-team-profile-card--header">
      <img :src="src" :alt="imageAlt" class="sd-team-profile-card--picture" loading="lazy">
    </header>
    <div class="sd-team-profile-card--content">
      <h4 class="sd-team-profile-card--name">{{name}}</h4>
      <h5 class="sd-team-profile-card--affiliation">{{affiliation}}</h5>
    </div>
  </article>
</template>

<script>
export default {
    name: 'TeamProfileCard',
    props: {
      src: String,
      name: String,
      affiliation: String,
    },
    computed: {
      imageAlt() {
        return 'Picture of ' + this.name;
      }
    }
}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_text.scss';
.sd-team-profile-card {
  display: inline-block;
  margin: 0 1rem 2rem 1rem;
  padding: 1rem;
  background-color: $very-dark-desaturated-blue;
}

.sd-team-profile-card--header {
  text-align:center;
}

.sd-team-profile-card--picture {
  display: inline-block;
  width: 200px;
  padding: 0;
  margin: 0;
}

.sd-team-profile-card--name {
  margin: 0.5rem 0 0;
  font-size: 1.2rem;
}

.sd-team-profile-card--affiliation {
  font-size: 1rem;
  margin: 0;
  color: $light-gray;
}
</style>