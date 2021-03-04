<template>
    <div>
        <li class="nav-item dropdown" v-if="notifications.length" style="margin-top: 10px;">
            <a id="navbarSubscription" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-bell"></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarSubscription">
                <li v-for="notification in notifications">
                    <a v-bind:href="notification.data.link" v-text="notification.data.message" style="white-space: normal" v-on:click="markAsRead(notification)"></a>
                </li>
            </ul>
        </li>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                notifications: false
            }
        },

        created() {
            axios.get('/profiles/' + window.App.user.name + '/notifications/')
                 .then(response => this.notifications = response.data);
        },

        methods: {
            markAsRead(notification){
                axios.delete('/profiles/' + window.App.user.name + '/notifications/' + notification.id);
            }
        }
    }
</script>
