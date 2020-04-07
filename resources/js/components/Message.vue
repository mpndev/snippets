<template>
    <div class="modal" :class="is_active_class">
        <div class="modal-background" @click="hideMessage()"></div>
        <div class="modal-content box" :class="[background_class, color_class]">
            <div class="columns is-marginless">
                <div class="column">
                    <p class="title is-3 has-text-centered">{{ message }}</p>
                </div>
            </div>
            <div class="columns">
                <div class="column is-offset-2 is-4">
                    <button v-if="run_callback" class="button is-success is-large is-fullwidth" @click="yes()">YES</button>
                </div>
                <div class="column is-4">
                    <button v-if="run_callback" class="button is-danger is-large is-fullwidth" @click="hideMessage()">NO</button>
                </div>
            </div>
        </div>
        <button class="modal-close is-large" aria-label="close" @click="hideMessage()"></button>
    </div>
</template>

<script>
    import Vue from "vue";

    export default {
        mounted() {
            Event.$on('show-message', this.showMessage)
        },
        data: () => {
            return {
                message: '',
                type: 'danger',
                background_classes: {
                    info: 'has-background-info',
                    success: 'has-background-success',
                    warning: 'has-background-warning',
                    danger: 'has-background-danger'
                },
                color_classes: {
                    info: 'has-text-white',
                    success: 'has-text-white',
                    warning: 'has-text-black',
                    danger: 'has-text-white'
                },
                callback: null,
                run_callback: false
            }
        },
        computed: {
            is_active_class() {
                return this.message.length ? 'is-active' : ''
            },
            background_class() {
                return this.background_classes[this.type];
            },
            color_class() {
                return this.color_classes[this.type];
            }
        },
        methods: {
            showMessage(data) {
                this.message = data.message
                this.type = data.type
                if (data.callback && typeof data.callback === "function") {
                    this.run_callback = true
                    this.callback = data.callback
                }
            },
            hideMessage() {
                this.message = ''
                this.type = 'danger'
                this.callback = null
            },
            yes() {
                this.message = ''
                this.run_callback = false
                this.callback()
            }
        }
    }
</script>
