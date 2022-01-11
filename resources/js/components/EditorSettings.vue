<template>
    <div class="modal" :class="show_editor_settings ? 'is-active' : ''">
        <div class="modal-background" @click="updateOptions"></div>
        <div class="modal-content box">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ $t('editor settings') }}</p>
                <button class="delete" aria-label="close" @click="updateOptions"></button>
            </header>
            <section class="modal-card-body">
                <div class="columns level has-background-grey-light is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Language') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select v-model="options.mode">
                                        <option v-for="(language, languga_index) in languages" :value="languga_index">{{ language }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-lighter is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Theme') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select v-model="options.theme" @change="setSettings">
                                        <option v-for="theme in themes">{{ theme }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-light is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Cursor blink rate') }}</p>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <div class="cotrol">
                                <input class="input" type="number" v-model="options.cursorBlinkRate">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 is-offset-2">
                        <p>{{ $t('Cursor height') }}</p>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <div class="cotrol">
                                <input class="input" type="number" v-model="options.cursorHeight">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-lighter is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Indent unit') }}</p>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <div class="cotrol">
                                <input class="input" type="number" v-model="options.indentUnit">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 is-offset-2">
                        <p>{{ $t('Tab size') }}</p>
                    </div>
                    <div class="column is-2">
                        <div class="field">
                            <div class="cotrol">
                                <input class="input" type="number" v-model="options.tabSize">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-light is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Smart Indent') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" v-model="options.smartIndent">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 is-offset-2">
                        <p>{{ $t('Indent with tabs') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="cotrol">
                                <input type="checkbox" v-model="options.indentWithTabs">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-lighter is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Electric chars') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" v-model="options.electricChars">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 is-offset-2">
                        <p>{{ $t('Line wrapping') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" v-model="options.lineWrapping">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-light is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Line numbers') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" v-model="options.lineNumbers">
                            </div>
                        </div>
                    </div>
                    <div class="column is-3 is-offset-2">
                        <p>{{ $t('Spellcheck') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" v-model="options.spellcheck">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-lighter is-marginless has-text-link">
                    <div class="column is-3">
                        <p>{{ $t('Autocorrect') }}</p>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <input type="checkbox" v-model="options.autocorrect">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns level has-background-grey-light is-marginless has-text-link">
                    <div class="column is-half">
                        <label class="radio">
                            <input type="radio" name="direction" value="ltr" v-model="options.direction">
                            {{ $t('Direction left to right') }}
                        </label>
                    </div>
                    <div class="column is-half">
                        <label class="radio">
                            <input type="radio" name="direction" value="rtl" v-model="options.direction">
                            {{ $t('Direction right to left') }}
                        </label>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['show_editor_settings'],
        data: () => {
            return {
                options: {
                    indentUnit: 2,
                    smartIndent: true,
                    tabSize: 2,
                    indentWithTabs: false,
                    electricChars: true,
                    direction: "ltr",// or "rtl"
                    mode: 'default',
                    theme: 'default',
                    lineWrapping: true,
                    lineNumbers: true,
                    readOnly: false,
                    undoDepth: 200,
                    autofocus: true,
                    cursorBlinkRate: 530,
                    cursorScrollMargin: 0,
                    cursorHeight: 1,
                    workTime: 200,
                    workDelay: 300,
                    spellcheck: false,
                    autocorrect: false,
                    autocapitalize: false,
                    line: true
                },
                Auth:Auth,
                languages: {
                    'default':'default',
                    'php':'PHP',
                    'css':'CSS',
                    'sql':'SQL',
                    'twig':'Twig',
                    'vue':'Vue',
                    'xml':'HTML',
                    'yaml':'YAML',
                    'javascript': 'javascript'
                },
                'themes': [
                    'default', '3024-day', '3024-night', 'abcdef', 'ambiance', 'ayu-dark', 'ayu-mirage', 'base16-dark',
                    'base16-light', 'bespin', 'blackboard', 'cobalt', 'colorforth', 'darcula', 'dracula',
                    'duotone-dark', 'duotone-light', 'eclipse', 'elegant', 'erlang-dark', 'gruvbox-dark', 'hopscotch',
                    'icecoder', 'idea', 'isotope', 'lesser-dark', 'liquibyte', 'lucario', 'material', 'material-darker',
                    'material-palenight', 'material-ocean', 'mbo', 'mdn-like', 'midnight', 'monokai', 'moxer', 'neat',
                    'neo', 'night', 'nord', 'oceanic-next', 'panda-syntax', 'paraiso-dark', 'paraiso-light',
                    'pastel-on-dark', 'railscasts', 'rubyblue', 'seti', 'shadowfox', 'solarized dark',
                    'solarized light', 'the-matrix', 'tomorrow-night-bright', 'tomorrow-night-eighties', 'ttcn',
                    'twilight', 'vibrant-ink', 'xq-dark', 'xq-light', 'yeti', 'yonce', 'zenburn'
                ]
            }
        },
        mounted() {
            if (this.Auth.check()) {
                this.options = this.Auth.getParsedSettings()
            }
            this.$emit('editor-options-was-updated', this.options)
        },
        methods: {
            updateOptions() {
                this.$emit('editor-options-was-updated', this.options)
            },
            setSettings() {
                if (this.Auth.check()) {
                    this.Auth.setStringifiedSettings(this.options)
                    axios.post(`/api/users/${this.Auth.user.id}/settings`, {
                        '_method': 'PUT',
                        'settings': this.Auth.getStringifiedSettings(),
                        'api_token': this.Auth.getApiToken()
                    })
                }
            },
        }
    }
</script>
