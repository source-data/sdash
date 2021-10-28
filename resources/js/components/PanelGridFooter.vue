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

        <section id="sd-contact-information" class="container-fluid bg-dark text-light">
            <b-row>
                <b-col class="sd-contact-label" cols="4" lg="2">
                    Contact
                </b-col>

                <b-col cols="8" lg="3">
                    SDash and SourceData Coordinator

                    <address class="mt-2">
                        <div class="sd-contact-name">{{ sdashCoordinator.name }}</div>

                        <a :href="'mailto:' + sdashCoordinator.email">
                            {{ sdashCoordinator.email }}
                        </a>
                    </address>
                </b-col>

                <b-col class="sd-contact-label ml-lg-auto" cols="4" lg="2">
                    Developed by
                </b-col>

                <b-col cols="8" lg="3">
                    <img src="/images/sourcedata_logo.png" alt="SourceData - Making data discoverable">
                </b-col>
            </b-row>
        </section>
    </footer>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "PanelGridFooter",
    data() {
        return {
            sdashCoordinator: {
                name: "Hannah Sonntag",
                email: "hannah.sonntag@embo.org",
            },
        };
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

.sd-contact-label,
.sd-contact-name {
    font-weight: bolder;
}
#sd-contact-information img {
    max-width: 20vw;
}
</style>