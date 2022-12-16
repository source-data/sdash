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
@import 'resources/sass/_text.scss';

#sd-beta-testing-banner {
    background-color: $vivid-orange;
    box-shadow: 0 -1px 0.2rem $vivid-orange, // box shadow at the top ...
                0  1px 0.2rem $vivid-orange; // ... and the bottom of the banner
    color: $mostly-black-blue;
    font-size: $font-size-xxs;
    padding: 0.15rem;

    position: absolute;
    top: 80px;
    left: -60px;

    text-align: center;
    transform: rotate(-35deg);
    width: 200px;
    z-index: 1;
}
#sd-beta-testing-banner.with-sidebar {
    left: -45px;
}
@media (min-width: 768px) {
    #sd-beta-testing-banner {
        font-size: $font-size-xs;
        left: -50px;
        padding: 0.35rem;
        top: 85px;
    }
    #sd-beta-testing-banner.with-sidebar {
        left: -30px;
    }
}
</style>