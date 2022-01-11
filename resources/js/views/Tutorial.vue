<template>
    <div>
        <article id="tutorial" class="info-content">
            <h2>Tutorial</h2>

            <p>
                <h3 hidden aria-hidden>Aims</h3>

                This quick tutorial will give you an overview of SDash most important features.
                <strong>You will learn how to:</strong>

                <ul>
                    <li>Upload figures</li>
                    <li>Add information, keywords and smart tags</li>
                    <li>Create a sharing group with other users or allow public sharing</li>
                    <li>Export your figures in order to use them outside of SDash</li>
                    <li>SEARCH/Browse for stuff</li>
                </ul>

            <p>
                <h3>Step 1. Upload a Figure to your Dashboard</h3>

                Drag and drop your result figure to import your result figure on your personal dashboard or use the
                "plus button" at the top right. Your SmartFigure is now on your personal dashboard only visible to you
                as indicated by the lock on the figure.

                <img src="/images/tutorial/upload-figure.png" alt="A screenshot showing how to upload a figure.">

            <p>
                <h3>Step 2. Give your SmartFigure more information</h3>

                Click on the figure to see and edit the details such as the title and the contributing authors.
                <br>
                Your coauthors will be notified and will have access to this SmartFigure that they are coauthoring.
                <br>
                Add a figure description. 
                <br>
                You can allow the smart tag engine to automatically detect key entities of your experiment from your
                description text in the figure. The smart tags will be categorized as "measured variable", "controlled
                variable", "Instrument/Methods" characterizing your experiment. You can also add any keyword to your
                figure which can allow you to search and filter your results according to keywords.

                <img src="/images/tutorial/figure-tagging.png" alt="A screenshot showing how to tag a figure.">

            <p>
                <h3>Step 3. Attach relevant files</h3>

                In the "source" tab you can link to external resources or attach raw data files and protocols to
                support this figure. 

            <p>
                <h3>Step 4. Share your figures with other users or publicly</h3>

                <img
                    src="/images/tutorial/add-figure-to-group.png"
                    alt="A screenshot showing how to add a figure to a group."
                    class="img-inline">

                You can share your SmartFigure at several levels.
                <br>
                You can either share the figure with groups of other SDash users or create a public link exposing your
                figure to the worldwide web.
                <br>
                For sharing in a group of SDash users you can either create a new group and select the members your
                trusted circle of collaborators, or you share the figure in an existing group you are already a member
                in. The groups that you are a member of are displayed on the left of your dashboard.
                <br>
                Creating a public link will create a landing page displaying your figure along with all information you
                have added. The QR code can be used in talks or poster presentations to make you research more visible.
                By revoking the public link, you can restrict access again.

            <p>
                <h3>Step 5. Discuss results, exchange ideas</h3>

                Figures shared in a group now have the two-way arrow label to indicate their shared status. In the
                "discuss" tab you can leave comments on your own figures. You can also comment on shared figures from
                your peers. The comments are visible for everybody with whom the figure is shared, allowing
                collaborative discussions.

            <p>
                <h3>Step 6. Export Your Data</h3>

                <img
                    src="/images/tutorial/figure-export.png"
                    alt="A screenshot showing how to export a figure."
                    class="img-inline">

                The download button gives you several options to download either only the figure, or a figure package
                including all the entered information as well as the  attached files.
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
    .img-inline {
        float: right;
        margin: 0 0 0 1.5rem;
        width: unset;
    }
}
</style>