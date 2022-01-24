<template>
    <div>
        <article v-if="user" id="user-profile">
            <header>
                <h2>{{ fullName }}</h2>

                <router-link
                    v-if="isAuthorized"
                    :to="{ name: 'useredit', params: { user_id: user.id } }"
                    id="link-to-edit-user-profile"
                    class="btn btn-desat-blue"
                >
                    <font-awesome-icon icon="edit" title="Edit user details" />
                    Edit profile
                </router-link>
            </header>

            <b-container fluid="lg" id="user-profile-info">
                <b-row v-if="user.email">
                    <b-col>
                        Email
                    </b-col>

                    <b-col md="auto">
                        <a
                            class="text-info"
                            :href="'mailto:' + user.email"
                            target="_blank"
                        >
                            {{ user.email }}
                        </a>
                    </b-col>
                </b-row>

                <b-row v-if="user.orcid">
                    <b-col>
                        ORCID
                    </b-col>

                    <b-col md="auto">
                        <a
                            class="text-info"
                            :href="'https://orcid.org/' + user.orcid"
                            target="_blank"
                        >
                            {{ user.orcid }}
                        </a>
                    </b-col>
                </b-row>

                <b-row v-if="user.institution_name">
                    <b-col>
                        Institution Name
                    </b-col>

                    <b-col md="auto">
                        {{ user.institution_name }}
                    </b-col>
                </b-row>

                <b-row v-if="user.institution_address">
                    <b-col>
                        Institution Address
                    </b-col>

                    <b-col md="auto">
                        {{ user.institution_address }}
                    </b-col>
                </b-row>

                <b-row v-if="user.department_name">
                    <b-col>
                        Department Name
                    </b-col>

                    <b-col md="auto">
                        {{ user.department_name }}
                    </b-col>
                </b-row>

                <b-row v-if="user.linkedin">
                    <b-col>
                        LinkedIn
                    </b-col>

                    <b-col md="auto">
                        <a
                            class="text-info"
                            :href="user.linkedin"
                            target="_blank"
                        >
                            {{ user.linkedin }}
                        </a>
                    </b-col>
                </b-row>

                <b-row v-if="user.twitter">
                    <b-col>
                        Twitter
                    </b-col>

                    <b-col md="auto">
                        <a
                            class="text-info"
                            :href="user.twitter"
                            target="_blank"
                        >
                            {{ user.twitter }}
                        </a>
                    </b-col>
                </b-row>

                <b-row>
                    <b-col>
                        Profile Image
                    </b-col>

                    <b-col md="auto">
                        <image-uploader
                            field="avatar"
                            ref="avatarUploader"
                            @crop-upload-success="avatarUploadSuccess"
                            v-model="showAvatarUploadDialog"
                            :width="300"
                            :height="300"
                            :url="avatarUploadUrl"
                            :method="requestMethod"
                            :headers="requestHeaders"
                            :params="requestParams"
                            :no-square="true"
                            lang-type="en"
                            img-format="jpg"
                        ></image-uploader>

                        <div class="avatar">
                            <b-overlay :show="changingAvatar" variant="dark">
                                <img
                                    :src="avatarUrl"
                                    :alt="'Avatar of ' + fullName"
                                />

                                <button
                                    class="edit-avatar text-xxs"
                                    v-if="isAuthorized"
                                    @click="toggleAvatarUploadDialog"
                                    title="Change avatar"
                                >
                                    <font-awesome-icon icon="pen" />
                                </button>

                                <div v-if="isAuthorized && user.avatar">
                                    <button
                                        id="sd-delete-avatar-button"
                                        class="delete-avatar text-xxs"
                                        @click="toggleAvatarDeleteDialog"
                                        title="Delete avatar"
                                    >
                                        <font-awesome-icon icon="trash-alt" />
                                    </button>

                                    <b-popover
                                        custom-class="sd-custom-popover"
                                        placement="top"
                                        show.sync="showAvatarDeleteDialog"
                                        target="sd-delete-avatar-button"
                                        triggers="click blur"
                                    >
                                        <div class="confirm-close-author-modal">
                                            <p>
                                                Do you really want to delete your avatar?
                                            </p>

                                            <div class="delete-buttons">
                                                <b-button
                                                    id="sd-delete-avatar-confirm-button"
                                                    variant="primary"
                                                    small
                                                    @click="deleteAvatar"
                                                >
                                                    Delete the avatar
                                                </b-button>

                                                <b-button
                                                    variant="outline-dark"
                                                    small
                                                    @click="toggleAvatarDeleteDialog"
                                                >
                                                    Cancel
                                                </b-button>
                                            </div>
                                        </div>
                                    </b-popover>
                                </div>
                            </b-overlay>
                        </div>
                    </b-col>
                </b-row>
            </b-container>

            <footer v-if="isProfileOfLoggedInUser">
                To delete your account, please send an email to
                <a
                    class="text-info"
                    href="'mailto:sourcedata@embo.org'"
                    target="_blank"
                >
                    sourcedata@embo.org </a
                >.
            </footer>
        </article>

        <div class="spinner-container">
            <b-spinner
                v-if="loading"
                variant="primary"
                label="Spinning"
            ></b-spinner>
        </div>
    </div>
</template>

<script>
import Axios from "axios";
import ImageUploader from "vue-image-crop-upload/upload-2.vue";
import { mapGetters, mapMutations } from "vuex";

export default {
    name: "UserProfile",
    components: {
        ImageUploader
    },
    props: ["user_id"],
    data() {
        return {
            changingAvatar: false,
            loading: false,
            user: null,
            error: null,
            showAvatarUploadDialog: false,
            showAvatarDeleteDialog: false,
            requestMethod: "POST",
            requestHeaders: {
                "X-XSRF-TOKEN": this.$cookies.get("XSRF-TOKEN")
            },
            requestParams: {
                _method: "PATCH"
            }
        };
    },
    computed: {
        ...mapGetters(["currentUser"]),
        isProfileOfLoggedInUser() {
            return this.currentUser.id === this.user.id;
        },
        isAuthorized() {
            return (
                this.isProfileOfLoggedInUser ||
                this.currentUser.role === "superadmin"
            );
        },
        fullName() {
            return this.user.firstname + " " + this.user.surname;
        },
        avatarUrl() {
            return this.user.avatar
                ? "/storage/avatars/" + this.user.avatar
                : "/images/default_avatar.jpg";
        },
        avatarUploadUrl() {
            return (
                process.env.MIX_API_URL + "/users/" + this.user.id + "/avatar"
            );
        }
    },
    watch: {
        user_id: function updateDisplayedUser() {
            this.fetchData();
        }
    },
    created() {
        this.fetchData();
    },
    methods: {
        ...mapMutations(["setCurrentUser"]),
        fetchData() {
            this.error = this.user = null;
            this.loading = true;
            return Axios.get("/users/" + this.user_id)
                .then(response => {
                    return (this.user = response.data.DATA);
                })
                .catch(error => {
                    this.error = error;
                })
                .then(() => {
                    this.loading = false;
                });
        },
        toggleAvatarUploadDialog() {
            this.showAvatarUploadDialog = !this.showAvatarUploadDialog;
        },
        avatarUploadSuccess(response) {
            this.user.avatar = response.DATA.avatar;
            if (this.isProfileOfLoggedInUser) {
                this.setCurrentUser(this.user);
            }
            this.showAvatarUploadDialog = false;
            this.$refs.avatarUploader.setStep(1);
        },
        toggleAvatarDeleteDialog() {
            this.showAvatarDeleteDialog = !this.showAvatarDeleteDialog;
        },
        deleteAvatar() {
            this.changingAvatar = true;
            this.toggleAvatarDeleteDialog();
            return Axios.delete("/users/" + this.user_id + "/avatar")
                .then(response => {
                    this.user.avatar = null;
                })
                .catch(error => {
                    this.error = error;
                })
                .then(() => {
                    this.changingAvatar = false;
                });
        },
    }
};
</script>

<style lang="scss" scoped>
@import "resources/sass/_colors.scss";
@import "resources/sass/_text.scss";

#user-profile {
    font-size: $font-size-md;
    margin: 2rem 5%;
}
@media (min-width: 768px) {
    #user-profile {
        margin: 6rem 5%;
    }
}

header {
    margin-bottom: 3rem;

    h2 {
        color: $vivid-orange;
        flex: auto;
        margin: 0;
        padding: 0.5rem 0;
    }

    #link-to-edit-user-profile {
        font-size: $font-size-sm;
        padding: 0.75rem 1rem;
    }
}
@media (min-width: 768px) {
    header {
        display: flex;
        margin-bottom: 6rem;
    }
}

#user-profile-info {
    padding: 0;
    margin-left: 0;

    .row:not(:last-child) {
        padding-bottom: 1.5rem;
    }

    .row .col:first-child {
        max-width: 250px;
    }

    .avatar {
        margin-bottom: 5px;
        position: relative;
        width: 150px;
    }

    .avatar img {
        width: 100%;
    }

    .avatar button {
        position: absolute;
        top: 0.25rem;
        color: white;
        border-radius: 50%;
        background-color: rgba(0, 0, 0, 0.25);
    }
    .avatar button,
    .avatar button * {
        border: none;
        outline: none;
    }
    .avatar button.edit-avatar {
        right: 0.25rem;
    }
    .avatar button.delete-avatar {
        left: 0.25rem;
    }
}

footer {
    margin-top: 100px;
}

.spinner-container {
    margin-top: 5rem;
    text-align: center;
}
</style>
