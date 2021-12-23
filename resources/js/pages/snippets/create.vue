<template>
    <div>
        <editor-settings :show_editor_settings="show_editor_settings"@editor-options-was-updated="updateEditorOptions"></editor-settings>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div class="columns" >
                        <div class="column is-three-fifths" v-if="Auth.check()"><b>{{ $t('Author') }}:</b> {{ Auth.getName() }}</div>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                        <div v-if="!snippet.public" class="column">
                            <button class="button is-danger fa fa-lock" @click="snippet.public = !snippet.public" :title="$t('This snippet is visible only to you!')"></button>
                        </div>
                        <div v-if="snippet.public" class="column">
                            <button class="button is-warning fa fa-unlock" @click="snippet.public = !snippet.public" :title="$t('This snippet is visible to everyone!')"></button>
                        </div>
                        <div class="column">
                            <button class="button is-info fa fa-cog" @click="show_editor_settings = true"></button>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
                            <div class="control">
                                <label for="title">{{ $t('Title') }}:</label>
                                <input class="input" id="title" type="text" :placeholder="$t('min symbols 1, max symbols 255')" v-model="snippet.title">
                                <span v-for="error in errors.title" class="title is-6 has-text-danger">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
                            <div class="control">
                                <label for="description">{{ $t('Description') }}:</label>
                                <input class="input" id="description" type="text" :placeholder="$t('max symbols 2000')" v-model="snippet.description">
                                <span v-for="error in errors.description" class="title is-6 has-text-danger">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
                            <div class="control">
                                <div><b>{{ $t('Tags') }}:</b></div>
                                <label for="tags"></label>
                                <input class="input" id="tags" type="text" :placeholder="$t('php, c#, full stack, bash')" v-model="fresh_tags">
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <div class="tags">
                                    <span v-for="(tag) in tags" class="tag is-success">{{ tag }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-9">
                <div class="columns">
                    <p class="column field">
                        <Editor :snippet="snippet" :options="editorOptions" @code-was-updated="codeWasUpdated"></Editor>
                        <span v-for="error in errors.body" class="title is-6 has-text-danger">{{ error }}</span>
                    </p>
                </div>
                <div class="columns">
                    <div class="column">
                        <button class="button is-success is-large is-fullwidth" @click="create()">{{ $t('CREATE') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import EditorSettings from '../../components/EditorSettings'
    import Editor from '../../components/Editor'

    export default {
        components: {
            EditorSettings: EditorSettings,
            Editor: Editor
        },
        data: () => {
            return {
                editorOptions: {},
                Auth: Auth,
                snippet: {
                    title: '',
                    description: '',
                    body: '',
                    public: false,
                },
                fresh_tags: '',
                errors: {
                    title: [],
                    description: [],
                    body: [],
                },
                show_editor_settings: false
            }
        },
        created() {
            this.warn({message: this.$t('Do not store sensitive data like passwords, tokens, etc...')})
        },
        computed: {
            tags() {
                let processed_fresh_tags = this.fresh_tags.replace(', ', ',')
                let processed_fresh_tags_array = processed_fresh_tags.split(',')
                let tags = processed_fresh_tags_array.map(item => item.trim()).filter(item => !!item.length)
                return [...new Set(tags)]
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('create a snippet')
        },
        methods: {
            validateForm() {
                if (this.snippet.title.trim().length < 1) {
                    this.errors.title.push(this.$t('Title is required.'))
                }
                if (this.snippet.title.trim().length > 255) {
                    this.errors.title.push(this.$t('Title cannot be more then 255 symbols.'))
                }
                if (this.snippet.description.trim().length > 2000) {
                    this.errors.description.push(this.$t('Description cannot be more then 2000 symbols.'))
                }
                if (this.snippet.body.length < 1) {
                    this.errors.body.push(this.$t('Snippet is required.'))
                }
                if (this.snippet.body.length > 100000) {
                    this.errors.body.push(this.$t('Snippet cannot be more then 100 000 symbols.'))
                }

                return (this.errors.title.length + this.errors.description.length + this.errors.body.length) < 1
            },
            resetErrors() {
                this.errors.title = []
                this.errors.description = []
                this.errors.body = []
            },
            create() {
                this.resetErrors()
                if (this.validateForm()) {
                    let response = axios.post('/api/snippets?api_token=' + this.Auth.getApiToken(), {
                        title: this.snippet.title,
                        description: this.snippet.description,
                        body: this.snippet.body,
                        settings: JSON.stringify(this.editorOptions),
                        public: this.snippet.public
                    }).then(response => {
                        return response
                    }).catch(error => {
                        this.error({message: error.toString()})
                    })
                    response.then(response => {
                        let snippet_id = response.data.id
                        this.$router.push({ name: 'snippets.show', params: {snippet: snippet_id} })
                        this.success({message: this.$t('Snippet was created successful.')})
                        if (this.tags.length) {
                            this.tags.map(tag => {
                                axios.post(`/api/tags?api_token=` + this.Auth.getApiToken(), {
                                    name: tag,
                                    snippet: snippet_id
                                }).then(inner_response => {
                                    this.success({message: `"${inner_response.data.name}" ${this.$t('tag was added to the snippet.')}`})
                                }).catch(inner_error => {
                                    this.error({message: inner_error.toString()})
                                })
                            })
                        }
                    })
                }
            },
            updateEditorOptions(options) {
                this.show_editor_settings = false
                this.updateStylesheet(options.theme)
                this.editorOptions = options
            },
            updateStylesheet(name) {
                const current_theme_name = this.editorOptions.theme
                const current_theme_link = document.querySelector(`link[data-current-theme="${current_theme_name}"]`)
                if (current_theme_link) {
                    current_theme_link.remove()
                }
                const stylesheet = document.createElement('link')
                stylesheet.setAttribute("data-current-theme", name)
                stylesheet.setAttribute("rel", "stylesheet")
                stylesheet.setAttribute("type", "text/css")
                stylesheet.setAttribute("href", `/css/themes/${name}.css`)
                document.getElementsByTagName("head")[0].appendChild(stylesheet);
            },
            codeWasUpdated(code) {
                this.snippet.body = code
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
