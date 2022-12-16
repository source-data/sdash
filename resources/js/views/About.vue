<template>
    <div>
        <article id="sd-about" class="info-content">
            <h2>About SDash</h2>

            <section>
                <h3 class="text-light">Aims</h3>

                SDash is an open source platform for sharing scientific results.

                <p>
                    SDash enables scientists to generate and share SmartFigures that link a scientific figure to the
                    underlying source data and structured machine-readable metadata. Users can manage their SmartFigures
                    to share them with groups of colleagues or make them public to share with the whole scientific
                    community. Users can comment and discuss initiating an early scientific dissemination of results.
                </p>
            </section>

            <section>
                <h3>SDash provides:</h3>

                <p>
                    <ul>
                        <li>
                            an intuitive way of sharing scientific results
                        </li>

                        <li>
                            direct access to underlying data for in-depth analysis
                        </li>

                        <li>
                            a rapid mechanism to disseminate scientific results
                        </li>

                        <li>
                            a painless route for organizing results and data
                        </li>
                    </ul>
                </p>
            </section>

            <section>
                <h3>How does it work?</h3>

                <p>
                    <img src="/images/sdash_infographic2.jpg" alt="A flow diagram showing how SDash works.">

                    Scientists have their own personal workspace where they upload their result figures by a simple drag
                    &amp; drop. Research results and underlying data are easily organized by linking them with local or
                    remote data files, computer scripts, and protocols. By creating or participating in groups of
                    collaborating peers, researchers can control the visibility of their content. Online commenting opens
                    the interdisciplinary dialogue to exchange ideas, promote critical debates and provides a way for early
                    dissemination of findings ultimately prepare your figures for publication.
                </p>

                <p>
                    Learn more:

                    <a
                        class="text-info"
                        href="https://elifesciences.org/labs/9d062a9b/smartfigures-dashboard-and-editor-tools-for-quick-and-easy-research-communication"
                    >
                        SmartFigures Dashboard and Editor: tools for quick and easy research communication
                    </a>
                </p>
            </section>
            <section>
                <h2>Credits</h2>
                <p>SDash is created and developed by the <a href="https://sourcedata.embo.org/" target="_blank" rel="noopener noreferrer">SourceData</a> team at <a href="https://www.embo.org/" target="_blank" rel="noopener noreferrer">EMBO</a> with the <a href="https://www.sib.swiss/">Swiss Insitute of Bioinformatics</a>.</p>
                <div class="sd-credits-box">
                    <team-profile-card src="/images/team/thomas_l.jpg" name="Thomas Lemberger" affiliation="Head of SourceData, EMBO"></team-profile-card>
                    <team-profile-card src="/images/team/hannah_s.jpg" name="Hannah Sonntag" affiliation="Project Co-ordination, EMBO"></team-profile-card>
                    <team-profile-card src="/images/team/stephen_p.jpg" name="Stephen Pewter" affiliation="Web Development, EMBO"></team-profile-card>
                    <team-profile-card src="/images/team/thomas_e.jpg" name="Thomas Eidens" affiliation="Web Development, EMBO"></team-profile-card>
                    <team-profile-card src="/images/team/orlin_t.jpg" name="Orlin Topalov" affiliation="Web Development, SIB"></team-profile-card>
                </div>
            </section>
        </article>

        <info-footer></info-footer>
    </div>
</template>

<script>
import InfoFooter from "@/components/InfoFooter";
import TeamProfileCard from "@/components/TeamProfileCard";

export default {
  name: "About",
  components: { InfoFooter, TeamProfileCard },
  data() {
    return {};
  },
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_text.scss';

#about-content {
    font-size: $font-size-md;
    margin: 4rem 5%;
}
@media (min-width: 768px) {
    #about-content {
        margin: 6rem 5%;
        max-width: 1095px;
    }
}

h2, h3 {
    color: $vivid-orange;
}
h2 {
    font-size: $font-size-xl;
}
h3 {
    font-size: $font-size-md;
}

p {
    margin-top: 3rem;
}

img {
    margin-bottom: 3rem;
    margin-top: 2rem;
    width: 100%;
}

.sd-credits-box {
    margin-top: 3rem;
    margin-left: -1rem;
    margin-right: -1rem;
}
</style>