<template>
    <div id="sd-beta-testing-banner" :class="{ 'with-sidebar': isSidebarPresent }">
        Beta Testing
    </div>
</template>

<script>
import { mapGetters } from "vuex"

export default {
    name: 'BetaTestingBadge',
    data() {
        return {}
    },
    computed: {
        ...mapGetters(["isSidebarPresent"]),
    }
}
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';

#sd-beta-testing-banner {
    background-color: $vivid-orange;
    box-shadow: 0 -1px 0.2rem $vivid-orange, // box shadow at the top ...
                0  1px 0.2rem $vivid-orange; // ... and the bottom of the banner
    color: $mostly-black-blue;
    padding: 0.35rem;

    position: absolute;
    top: 85px;
    left: -50px;

    text-align: center;
    transform: rotate(-35deg);
    width: 200px;
}

#sd-beta-testing-banner.with-sidebar {
    left: -30px;
}
@media (min-width: 768px) {
    #sd-beta-testing-banner.with-sidebar {
        left: -30px;
    }
}
</style>