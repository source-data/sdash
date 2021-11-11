<template>
    <div class="sd-group-user-icon-container">
        <b-badge pill variant="info" class="sd-group-user--admin-badge" v-if="role=='admin'">Admin</b-badge>
        <avatar :username="user.firstname + ' ' + user.surname" :size="32" class="sd-group-user-avatar"></avatar>
        <div class="sd-group-user--info-wrapper">
            <router-link :to="{name: 'user', params: {user_id: user.id}}">
                <h4 class="sd-group-user--name">{{ user.firstname }} {{ user.surname }}</h4>
            </router-link>
            <p class="sd-group-user--affiliation">
                {{ user.institution_name }}
            </p>
        </div>
    </div>
</template>

<script>


export default {

    name: 'GroupUserIcon',
    props: {
        user: {
            type: Object,
            required: true,
        },
        role: {
            type: String,
            required: true,
            validator(val) {
                return ['user', 'admin'].indexOf(val) !== -1
            }
        }
    }

}
</script>

<style lang="scss">
.sd-group-user-avatar {
    margin: 0.2rem 0.4rem 0.2rem 0;
}
    .sd-group-user-icon-container {
        position: relative;
        display:inline-flex;
        flex-wrap:nowrap;
        justify-content:flex-start;
        align-items: center;
        text-align: left;
        padding: 0.25rem 0.5rem;
        background: #fff;
        margin: 0.25rem;
        border-radius: 0.85rem;
        box-shadow: 0 2px 6px #cbdae8;
    }

    .sd-group-user--admin-badge {
        position: absolute;
        top:-11px;
        right:6px;
    }

.sd-group-user--name {
    font-size: 1rem;
    line-height: 1.2;
    margin:0;
    padding:0;
    display: block;
    width:100%;
}

.sd-group-user--affiliation {
    font-size: 0.85rem;
    margin: 0;
    line-height:1.2;
}
</style>