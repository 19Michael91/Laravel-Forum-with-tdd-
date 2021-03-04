require('./bootstrap');

window.Vue = require('vue');
window.events = new Vue();

window.flash = function(message, level = 'success'){
    window.events.$emit('flash', {message, level});
};

Vue.component('flash', require('./components/Flash.vue').default);
Vue.component('reply', require('./components/Reply.vue').default);
Vue.component('replies', require('./components/Replies.vue').default);
Vue.component('favorite', require('./components/Favorite.vue').default);
Vue.component('new-reply', require('./components/NewReply.vue').default);
Vue.component('subscribe-button', require('./components/SubscribeButton.vue').default);
Vue.component('paginator', require('./components/Paginator.vue').default);
Vue.component('user-notifications', require('./components/UserNotifications.vue').default);
Vue.component('avatar-form', require('./components/AvatarForm.vue').default);
Vue.component('image-upload', require('./components/ImageUpload.vue').default);
Vue.component('wysiwyg', require('./components/Wysiwyg.vue').default);
Vue.component('thread-view', require('./pages/Thread.vue').default);

const app = new Vue({
    el: '#app'
});
