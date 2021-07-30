<template>
 <div class="sd-group-member-wrapper">
    <div class="sd-group-member-icon-wrapper">
        <font-awesome-icon icon="user-plus" size="xs" class="sd-group-icon"/>
    </div>
        <span class="sd-group-icon-text">{{caption}}</span>
 </div>
</template>

<script>

export default {

    name: 'GroupMemberRequestIcon',
    props: { caption: String }

}
</script>

<style lang="scss" scoped>
.sd-group-member-icon-wrapper {
  display: inline-flex;
    align-items: center;
    justify-content: center;
    background: darken(#ff9803, 10%);
    border-radius: 50%;
    line-height: 20px;
    width: 20px;
    height: 20px;
    color: #fff;
}

.sd-group-icon-text {
color: #fff;
}

.sd-group-member-wrapper {
    background-color: #ff9803;
    display: inline-block;
    padding: 2px 8px 2px 4px;
    border-radius: 12px;
}
</style>