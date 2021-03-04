<template>
    <div>
        <input id="trix" type="hidden" v-bind:name="name" v-bind:value="value">
        <trix-editor ref="trix" id="replyBody" input="trix" v-bind:placeholder="placeholder"></trix-editor>
    </div>
</template>

<script>
    import Trix from 'trix';

    export default{
        props: ['name', 'value', 'placeholder', 'shouldClear'],

        mounted(){
            this.$refs.trix.addEventListener('trix-change', e => {
                this.$emit('input', e.target.innerHTML);
            });

            this.$watch('shouldClear', () => {
                this.$refs.trix.value = '';
            });
        }
    }
</script>
