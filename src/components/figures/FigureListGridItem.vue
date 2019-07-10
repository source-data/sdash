<template>
    <section class="list-grid-item" :class="{ 'list-grid__expanded': isExpanded }">
        <div class="list-grid-image-container" @click.prevent="toggleExpanded" ref="imageContainer" tabindex="0">
            <img :src="panel.graphic.imgsrc" :alt="panel.label" class="list-grid-image">
            <div class="css_arrow"></div>
            <div class="list-grid-caption">
                <div class="list-grid-caption--owner">
                    <user-icon :name="panel.figure.owner"></user-icon>
                    <span class="list-grid-caption--owner-name">{{panel.figure.owner}}</span>
                </div>
                <div class="list-grid-caption--label">{{ panel.label }}</div>
            </div>
            <span class="list-grid-file-type" v-if="panel.subtype">
                {{ panel.subtype }}
            </span>
        </div>
        <div class="list-grid-extra" ref="listGridExtra">
            <button type="button" aria-label="Close" class="close list-grid-extra--close" @click.prevent="toggleExpanded"><span aria-hidden="true">&times;</span></button>
            <div class="list-grid-extra--wrapper">
                <div class="list-grid-extra--image-container">
                    <div class="list-grid-extra--image-wrapper">
                        <img :src="panel.graphic.imgsrc" :alt="panel.label" class="list-grid-extra--image" ref="listGridImage">
                    </div>
                </div>
                <div class="list-grid-extra--info-container">
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quis cum eaque rem ipsam tempora vel possimus culpa aspernatur hic adipisci, quia quibusdam facere expedita iste. Itaque unde placeat saepe iste!
                </div>
            
            </div>
        </div>
    </section>
</template>
 
<script>

import {Bus} from '@/bus';
import userIcon from '@/components/user/userIcon'

export default {
 
    name: 'FigureListGridItem',
    components: { userIcon, },
    props: ['panel'],
 
    data(){
 
        return {
            isExpanded: false
            
        }
 
    }, /* end of data */
 
    methods:{
 
        toggleExpanded(){

            let vm = this;

            vm.isExpanded = !vm.isExpanded;
            if(vm.isExpanded){ 

                vm.$emit('expanded', {id:vm.panel.panel_id});
                vm.$scrollTo(vm.$refs.listGridExtra, 300, {offset:-200} );
                setTimeout(function(){ vm.$refs.listGridImage.style.display="inline-block" }, 500);
            } else {
                vm.$refs.listGridImage.style.display="none";
            }

        }
    },
    mounted: function () {
        let vm = this;
        Bus.$on("close-panels", (obj) => { if(vm.isExpanded && obj.id !== vm.panel.panel_id) vm.isExpanded = false; });
    }

 
}
</script>
 
<style scoped>
   
    *, *:before, *:after {
   
        box-sizing: border-box;
   
    }

    .list-grid-item {
        box-sizing: border-box;
        height: 280px;
        min-width: 240px;
        margin:8px;
        cursor:pointer;
        transition: all 0.3s ease-in;
    }

    .list-grid-item.list-grid__expanded {

        margin-bottom:610px;

    }

    .list-grid-caption {
        position: absolute;
        top: 0;
        width: 100%;
        padding: 11px;
        background-color: rgb(51, 51, 51);
        color: #fff;
        opacity: 0.4;
        transition: opacity 150ms linear;
    }

    .list-grid-caption--owner {
        display: flex;
    }

    .list-grid-caption--owner .userIcon {
        margin-right: 1em;
    }

    .list-grid-caption--owner-name {
        vertical-align: middle;
        font-weight: bold;
        line-height: 35px;
    }

    .list-grid-caption--label {
        padding-left: 54px;
        margin-top: -12px;
    }

    .list-grid-item:hover .list-grid-caption,
    .list-grid-item:focus .list-grid-caption,
    .list-grid-item:active .list-grid-caption {
        opacity: 0.9;
    }

    .list-grid-image-container {
        height:100%;
        background-color:#3c3c3c;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .list-grid-image {
        display:block;
        max-height: 100%;
        width: auto;


    }
    
    .list-grid-file-type {
        position: absolute;
        bottom: 6px;
        right: 6px;
        padding: 3px 12px;
        background-color: #9e33a5;
        color: #fff;
        border-radius: 16px;
        line-height: 18px;
        opacity: 0.4;
        transition: all 200ms linear;
    }

    .list-grid-item:hover .list-grid-file-type,
    .list-grid-item:focus .list-grid-file-type,
    .list-grid-item:active .list-grid-file-type {
        opacity:0.8;
    }

    .list-grid-extra {
        position: absolute;
        left:0;
        width: 100%;
        background-color: #222;
        color: #ddd;
        font-size: 16px;
        margin-top: 6px;
        max-height: 0;
        height: 0;
        overflow: hidden;
        transition: all 0.3s ease-in;

    }

    .list-grid__expanded .list-grid-extra {
        max-height: 600px;
        height:600px;
    }

    .list-grid-extra--close {
        position: absolute;
        font-size: 2em;
        top:4px;
        right:8px;
    }

    .list-grid-extra--wrapper {
        height: 100%;
        display:flex;
        flex-wrap: nowrap;

    }

    .list-grid-extra--wrapper > div {
        padding: 48px;

    }

    .list-grid-extra--image-container {
        flex: 1 0 50%;
        display: flex;

    }

    .list-grid-extra--image-wrapper {

        height: 100%;
        width: 100%;
        text-align:center;

    }

    .list-grid-extra--image {
        max-width: 100%;
        max-height: 100%;
        display:none;
    }



    .css_arrow {
        display: none;
    }

    .list-grid__expanded .css_arrow {
        display: block;
        width: 0px;
        height: 0px;
        border: solid 15px transparent;
        border-bottom: solid 20px #222;
        border-top: none;
        position: absolute;
        bottom: -6px;
    }

</style>