<template>
    <div style="margin-top: 20px;" v-if="signedIn">
        <div class="form-group">
            <wysiwyg name="body"
                     v-model="body"
                     placeholder="Have something to say?"
                     v-bind:shouldClear="completed"></wysiwyg>
        </div>

        <button type="submit"
                class="btn btn-default"
                v-on:click="addReply">Post</button>
    </div>

    <p style="margin-top: 20px;" class="text-center" v-else>
        Please <a href="/login">sing in</a> to participate in this discussion.
    </p>
</template>

<script>
    import 'at.js';
    import 'jquery.caret';

    export default {
        data(){
            return {
                body: '',
                completed: false,
            };
        },

        mounted(){
            $('#replyBody').atwho({
    		    at: "@",
    		    delay: 750,
    		    callbacks: {
    			    remoteFilter: function(query, callback) {
    					$.getJSON("/api/users", {name: query}, function(usernames) {
    						callback(usernames);
    					});
    			    }
    		  	}
    		})
        },

        methods: {
            addReply(){
                axios.post(location.pathname + '/replies', { body: this.body })
                     .catch(error => {
                         flash(error.response.data, 'danger');
                     })
                     .then(({data}) => {
                         this.body = '';
                         this.completed = true;

                         flash('Your reply has been posted.');

                         this.$emit('created', data);
                     });
            },
        }
    }
</script>
