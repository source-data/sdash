<template>
    <b-container fluid class="sd-info-bar">
        <b-row>
            <b-col offset-sm="2" sm="10" class="mt-4 mb-2 sd-info-bar-title-wrapper">
                <slot name="above-title">
                </slot>
                <slot name="title">
                </slot>
                <p class="sd-info-bar-text">
                    <slot name="text"></slot>
                </p>
                <slot name="footer">
                </slot>
            </b-col>
        </b-row>
    </b-container>

</template>

<script>

export default {

    name: 'InfoBar',
    data(){

        return {

        }

    }, /* end of data */

    methods:{ //run as event handlers, for example

        methodName(){
            //do stuff here
        }

    }

}
</script>

<style lang="scss">

.sd-info-bar {
    background-color: #ebf5ff;
    padding-bottom: 1em;
    box-shadow: 0px 2px 4px -2px #656598;
}

.sd-info-bar-title-wrapper {
    padding-left:40px;
}

.sd-info-bar-text {
    max-width: 600px;
    // color: #eee;
}
</style>