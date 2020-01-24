<template>
 <div class="sd-group-title-icon-wrapper">
    <div class="sd-group-icon-wrapper">
        <font-awesome-icon icon="users" size="xs" class="sd-group-icon"/>
    </div>
        <span class="sd-group-icon-text">User Group</span>
 </div>
</template>

<script>

export default {

    name: 'GroupTitleIcon'

}
</script>

<style lang="scss">
.sd-group-icon-wrapper {
display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #15769f;
    border-radius: 50%;
    line-height: 20px;
    width: 20px;
    height: 20px;
    color: #fff;
}

.sd-group-icon-text {
color: #fff;
}

.sd-group-title-icon-wrapper {
    background-color: #82b6cc;
    display: inline-block;
    padding: 2px 8px 2px 4px;
    border-radius: 12px;
}
</style>