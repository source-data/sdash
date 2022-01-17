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
                    <img src="/images/group.svg" alt="A stylized image of a group, consisting of 3 people">
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
                    <img src="/images/loupe.svg" alt="A stylized image of a loupe and documents">
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

                <b-col class="mr-lg-auto" cols="8" lg="3">
                    <a href="https://sourcedata.embo.org/">
                        <img src="/images/logos/sourcedata.png" alt="SourceData - Making data discoverable">
                    </a>
                </b-col>
            </b-row>
        </section>

        <section id="sd-partners-information" class="container-fluid bg-light text-dark">
            <b-row>
                <b-col class="sd-partners-label pb-sm-2" lg="2">
                    Partners
                </b-col>

                <b-col class="sd-partners-list" lg="10">
                    <a href="https://www.embo.org/">
                        <img src="/images/logos/embo.svg" alt="EMBO - European Molecular Biology Organization">
                    </a>

                    <a class="d-flex" href="https://www.biologie.hu-berlin.de/en/gruppenseiten-en/sfb1315">
                        <img class="sfb-image" src="/images/logos/sfb.svg" alt="SFB logo">
                    </a>

                    <a href="https://www.sib.swiss/">
                        <img src="/images/logos/sib.png" alt="SIB - Swiss Institute of Bioinformatics">
                    </a>
                </b-col>
            </b-row>
        </section>

        <div id="sd-fine-print" class="bg-light text-dark">
            &copy; 2021 SDASH.sourcedata.io
            <span class="fine-print-delimiter">|</span>
            <a href="">Imprint</a>
            <span class="fine-print-delimiter">|</span>
            <a href="">Privacy Policy</a>
        </div>

        <b-button @click="scrollToTop()" variant="primary" class="back-to-top">
            <font-awesome-icon icon="angle-double-up" />
        </b-button>
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
    methods: {
        scrollToTop() {
            window.scrollTo(0, 0);
        },
    }
}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

#sd-panel-grid-footer > section {
    padding: 5rem 7.5vw;
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
    max-height: 12rem;
}

.sd-contact-label,
.sd-contact-name,
.sd-partners-label {
    font-weight: bolder;
}
#sd-contact-information img {
    max-height: 7rem;
    max-width: 80%;
}

#sd-partners-information a,
#sd-partners-information h5,
#sd-fine-print a {
    color: $mostly-black-blue;
}
#sd-partners-information img {
    height: 7rem;
    max-width: 100%;
}

.sd-partners-list {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    gap: 1rem;
}
@media (min-width: 768px) {
    .sd-partners-list {
        flex-direction: row;
        gap: 2rem;
    }
}

#sd-fine-print {
    padding-bottom: 1rem;
    text-align: center;
}
.fine-print-delimiter {
    margin-left: 0.75rem;
    margin-right: 0.75rem;
    user-select: none;
}

// To position the back-to-top button in the bottom right of the footer, its closest ancestor has to be positioned as
// well.
#sd-panel-grid-footer {
    position: relative;
}
.back-to-top {
    position: absolute;
    bottom: 0;
    right: 0;
    height: 3rem;
    width: 3rem;
    border-radius: 0;
}
</style>