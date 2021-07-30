<template>
    <b-container fluid class="sd-info-bar">
        <b-row>
            <b-col sm="10" class="mt-4 mb-2">
                <slot name="above-title">
                </slot>
                <slot name="title">
                </slot>
                <p class="sd-info-bar-text">
                    <slot name="text"></slot>
                </p>
                <slot name="extra-content">
                </slot>
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
    padding: 10px 48px;
    overflow: hidden;
    background: #e9eef5;
}

.sd-info-bar-text {
    max-width: 600px;
}
</style>