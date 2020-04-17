<style>
    .CodeMirror {
        border-radius: 5px;
        min-height: 7.5vh;
        height: auto;
    }
</style>

<template>
    <codemirror id="body" :options="options" placeholder="min symbols 1, max symbols 100 000" v-model="newCode" @input="onCmCodeChange"></codemirror>
</template>

<script>
    import { codemirror } from 'vue-codemirror'
    import 'codemirror/mode/javascript/javascript.js'
    import 'codemirror/mode/php/php.js'
    import 'codemirror/mode/htmlmixed/htmlmixed.js'
    import 'codemirror/mode/css/css.js'
    import 'codemirror/mode/yaml/yaml.js'
    import 'codemirror/mode/sql/sql.js'
    import 'codemirror/mode/twig/twig.js'
    import 'codemirror/mode/vue/vue.js'
    import 'codemirror/lib/codemirror.css'

    export default {
        props: [
            'snippet',
            'options'
        ],
        data: () => {
            return {
                newCode: ''
            }
        },
        components: {
            codemirror
        },
        created() {
            this.newCode = this.snippet.body ? this.snippet.body : ''
        },
        methods: {
            onCmCodeChange() {
                this.$emit('code-was-updated', this.newCode)
            }
        }
    }
</script>
