<template>
    <div>
        <div class="level">
            <img v-bind:src="avatar" alt="avatar" width="100" height="100" class="mr-1">
            <h1 v-text="user.name"></h1>
        </div>

        <form v-if="canUpdate" method="post" enctype="multipart/form-data">
            <image-upload name="avatar" class="mr-1" v-on:loaded="onLoad"></image-upload>
        </form>
    </div>
</template>

<script>
    import ImageUpload from './ImageUpload.vue';

    export default {
        props: ['user'],

        components: { ImageUpload },

        data() {
            return {
                avatar: this.user.avatar_path,
            };
        },

        computed: {
            canUpdate() {
                return this.authorize(user => user.id === this.user.id)
            }
        },

        methods: {
            onLoad(avatar){
                this.avatar = avatar.src;

                this.presist(avatar.file);
            },

            presist(avatar){
                let data = new FormData();
                data.append('avatar', avatar);
                axios.post('/users/${this.user.name}/avatar', data)
                     .then(() => flash('Avatar uploaded'));
            }
        }
    }
</script>
