<template>
    <b-container fluid class="sd-info-bar">
        <b-row>
            <b-col sm="10" class="mt-4 mb-2 sd-info-bar-title-wrapper">
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
    padding-bottom: 1em;
    overflow: hidden;
}

.sd-info-bar-title-wrapper {
    padding-left:62px;
    margin-left:300px;
}

.sd-info-bar-text {
    max-width: 600px;
    // color: #eee;
}
</style>