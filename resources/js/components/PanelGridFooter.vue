<template>
    <footer id="sd-panel-grid-footer">
        <section v-if="isLoggedIn" id="sd-entice-logged-in-user" class="container-fluid bg-primary">
            <b-row>
                <b-col md="6" class="call-to-action-text">
                    <div class="text-light text-lg font-weight-bolder">
                        Do you want to share your results within a special group?
                    </div>

                    <div class="text-lg font-weight-bolder">
                        <router-link :to="{name: 'creategroup'}">
                            Start a group.
                        </router-link>
                    </div>
                </b-col>

                <b-col md="6" class="call-to-action-image d-none d-md-block">
                    <img src="/images/group.svg" alt="">
                </b-col>
            </b-row>
        </section>

        <section v-else id="sd-entice-guest" class="container-fluid bg-primary">
            <b-row>
                <b-col md="5" class="call-to-action-text">
                    <div class="text-lg font-weight-bolder">
                        <span class="text-light">Share research results</span> with
                        your colleagues, the science community, and the world!
                    </div>

                    <div>
                        <router-link :to="{name: 'register'}">Register here</router-link> and get started.
                    </div>
                </b-col>

                <b-col md="7" class="call-to-action-image d-none d-md-block">
                    <img src="/images/lupe.svg" alt="">
                </b-col>
            </b-row>
        </section>

        <section class="container-fluid">

        </section>
    </footer>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "PanelGridFooter",
    data() {
        return {};
    },
    computed: {
        ...mapGetters([
            "isLoggedIn",
        ]),
    },
}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

#sd-panel-grid-footer > * {
    padding: 5vw 7.5vw;
}

.call-to-action-text {
    margin: auto 0;
    padding-right: 5vw;
}
.call-to-action-text a {
    color: $mostly-black-blue;
    text-decoration: underline;
}
.call-to-action-image {
    margin: auto 0;
    padding: 0 5vw;
}
.call-to-action-image img {
    display: block;
    margin: 0 auto;
    width: 100%;
}
#sd-entice-logged-in-user .call-to-action-image img {
    max-width: 25vw;
}
</style>