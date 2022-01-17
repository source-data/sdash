<template>
    <div>
        <article id="tutorial" class="info-content">
            <h2>Tutorial</h2>

            <section>
                <h3 hidden aria-hidden>Aims</h3>

                <p>
                    This quick tutorial will give you a short overview of SDash's most important features.
                    <strong>You will learn how to:</strong>

                    <ul>
                        <li>Use the Dashboard</li>
                        <li>Add information, keywords and smart tags</li>
                        <li>Create a sharing group with other users or allow public sharing</li>
                        <li>Export your figures in order to use them outside of SDash</li>
                    </ul>
                </p>
            </section>

            <section>
                <h3>Step 1. Use the Dashboard</h3>

                <p>
                    Welcome to your Dashboard. Here you find SmartFigures you have created yourself as well as SmartFigures
                    that were shared with you or are publicly available. You can search for authors or keywords to discover
                    your peers' research.

                    <img
                        src="/images/tutorial/01_dashboard.png"
                        alt="The SmartFigure dashboard, showing a grid of scientific figures.">
                </p>

                <p>
                    To create your first SmartFigure simply drag and drop your result figure from your file browser to the
                    Dashboard or use the "plus button" at the top right. Your SmartFigure is now on your personal dashboard
                    only visible to you.

                    <img
                        src="/images/tutorial/02_drag-and-drop_demo.gif"
                        alt="A video showing the drag-and-drop figure upload on the dashboard.">
                </p>
            </section>

            <section>
                <h3>Step 2. Give your SmartFigure more information</h3>

                <p>
                    Click on the figure to see and edit the details such as the title, the description and the contributing
                    authors.
                    <br>
                    When giving your SmartFigure a description you can allow the smart tag engine to automatically detect
                    key entities of your experiment from the text you enter. The smart tags will be categorized as
                    "measured variable", "controlled variable", "Instrument/Methods" characterizing your experiment. You
                    can also add any keyword to your figure which can allow you to search and filter your results according
                    to keywords. 

                    <img
                        src="/images/tutorial/03_panel-detail-view.png"
                        alt="The panel detail view with more information about a single SmartFigure.">
                </p>

                <p>
                    Credit your colleagues by including them in the list of authors. Your coauthors will be notified and
                    will have access to this SmartFigure depending on their role.

                    <img
                        src="/images/tutorial/04_edit-authors.gif"
                        alt="A video showing how to edit a SmartFigure's authors.">
                </p>
            </section>

            <section>
                <h3>Step 3. Attach relevant files</h3>

                <p>
                    You can attach your underlying research data, external resources, scripts and protocols or anything
                    that is relevant for your SmartFigure in the "Sources" tab. This way you will never have to look for
                    these files.

                    <img
                        src="/images/tutorial/05_attach-file.gif"
                        alt="A video showing how to add a source file to a SmartFigure.">
                </p>
            </section>

            <section>
                <h3>Step 4. Share your figures with other users or publicly</h3>

                <p>
                    <img
                        src="/images/tutorial/06_sharing-options.png"
                        alt="A closeup of the sharing options available for a SmartFigure.">
                </p>

                <p>
                    You have full control over who has access to your SmartFigure. By default, your SmartFigure is private
                    and only accessible to yourself. But you have the options to:

                    <ul>
                        <li>
                            share the SmartFigure with <strong>groups of other SDash users:</strong> You can either create
                            a new group and select the members from your trusted circle of collaborators, or you share the
                            figure in an existing group you are already a member of. The groups that you are a member of
                            are displayed on the left bar on your dashboard.
                        </li>
                        <li>
                            create <strong>a public link</strong> exposing your SmartFigure to everyone you provide with
                            this link. This link is also available as a QR code for talks or poster presentations to make
                            you research more visible. By revoking the public link, you can restrict access again.
                        </li>
                        <li>
                            Make your SmartFigure public: This will make your SmartFigure publicly available and searchable
                            on the public dashboard and is therefore visible to the web.
                        </li>
                    </ul>

                    The sharing status of your SmartFigures is indicated by the little lock symbol or the arrows in the
                    lower right of the figure. Figures shared in a group now have the two-way arrow label to indicate their
                    shared status.
                </p>
            </section>

            <section>
                <h3>Step 5. Export Your Data</h3>

                <p>
                    The download button gives you several options to download either only the figure, or a figure package
                    including all the entered information as well as the attached files.

                    <img
                        src="/images/tutorial/07_smartfigure-export.png"
                        alt="A closeup of the options for exporting a SmartFigure."
                        class="img-sm">
                </p>
            </section>
        </article>

        <info-footer></info-footer>
    </div>
</template>

<script>
import InfoFooter from "@/components/InfoFooter";

export default {
  name: "Tutorial",
  components: { InfoFooter },
  data() {
    return {};
  },
};
</script>

<style lang="scss" scoped>
@media (min-width: 568px) {
    .img-sm {
        display: block;
        margin: 1rem 0;
        height: 15rem;
    }
}
</style>