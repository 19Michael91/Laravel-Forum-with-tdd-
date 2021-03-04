<template>
    <div v-bind:id="'reply-'+id" class="card" style="margin-top: 20px;">
        <div class="card-header" v-bind:class="isBest ? 'card-success bg-success text-white' : 'card-default bg-light'">
            <div class="level">
                <h5 class="flex">
                    <a v-bind:href="'/profiles/'+reply.owner.name" v-text="reply.owner.name"></a>
                     said <span v-text="ago"></span>
                </h5>

                <div v-if="signedIn">
                    <favorite v-bind:reply="reply"></favorite>
                </div>

            </div>
        </div>

        <div class="card-body" style="padding-bottom: 0;">
            <div class="body">
                <div v-if="editing">
                    <form v-on:submit="update" id="updateReplyForm">
                        <div class="form-group">
                            <wysiwyg v-model="body"></wysiwyg>
                            <!-- <textarea class="form-control" v-model="body" required></textarea> -->
                        </div>

                        <button class="btn btn-primary btn-sm mt-1 mr-1">Update</button>
                        <button type="button" class="btn btn-link btn-sm mt-1" v-on:click="cancel">Cancel</button>
                    </form>
                </div>

                <div v-else v-html="body"></div>
            </div>
            <hr>
        </div>

        <div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', thread)" style="padding-left: 15px; padding-bottom: 15px;">
            <div v-if="authorize('owns', reply)">
                <button type="button" class="btn btn-info btn-sm mr-1" v-on:click="editing = true">Edit</button>
                <button type="button" class="btn btn-danger btn-sm mr-1" v-on:click="destroy">Delete</button>
            </div>
            <button type="button" class="btn btn-default btn-sm ml-a" v-on:click="markBestReply" v-if="authorize('owns', thread)">Best Reply?</button>
        </div>
    </div>
</template>



<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['reply'],

        components: { Favorite },

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                thread: window.thread,
            };
        },

        computed: {
            isBest(){
                return this.thread.best_reply_id == this.id;
            },
            ago(){
                return moment(this.reply.created_at).fromNow() + '...';
            },
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.id, {body: this.body,})
                     .catch(error => {

                         flash(error.response.data, 'danger');
                     });

                this.editing = false;

                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.id);

                this.$emit('deleted', this.id);

            },

            cancel(){
                this.editing = false;
                this.body = this.reply.body;
            },

            markBestReply(){

                axios.post('/replies/' + this.id + '/best');

                this.thread.best_reply_id = this.id;
            },
        }
    }
</script>
