<template>
    <div>
        <editor-settings v-if="snippet_copy" :show_editor_settings="show_editor_settings" @editor-options-was-updated="updateEditorOptions"></editor-settings>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div v-if="snippet.id">
                        <button class="button is-success fa fa-clipboard" title="copy code to the clipboard"></button>
                        <button class="button is-info fa fa-eye" title="show the snippet" @click="show(snippet)"></button>
                        <button v-if="Auth.check() && Auth.isOwner(snippet)" class="button is-danger fa fa-trash-alt" title="delete the snippet" @click="destroy(snippet)"></button>
                        <button v-if="Auth.check()" class="button is-info fa fa-cog is-pulled-right" @click="show_editor_settings = true"></button>
                    </div>
                    <ring-loader v-else class="is-narrow"></ring-loader>
                    <hr>
                    <div>
                        <p v-if="snippet.user"><b>Author:</b> {{ snippet.user.name }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Title:</b> {{ snippet.title }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div class="field">
                        <div v-if="snippet.id" class="control">
                            <label for="description">Description:</label>
                            <input class="input" id="description" type="text" placeholder="max symbols 2000" v-model="snippet_copy.description">
                            <span v-for="error in errors.description" class="title is-6 has-text-danger">{{ error }}</span>
                        </div>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Created: </b> {{ snippet.created_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Last update: </b> {{ snippet.updated_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id" class="field">
                            <div class="control">
                                <div><b>Tags:</b></div>
                                <label for="tags"></label>
                                <input class="input" id="tags" type="text" placeholder="php, c#, full stack, bash'" v-model="fresh_tags">
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <div class="tags">
                                    <span v-for="(tag) in tags" class="tag is-success">{{ tag }}</span>
                                </div>
                            </div>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.forks_quantity > 0" v-for="fork in snippet.forks"><b>Fork:</b> <a :href="'/snippets/' + fork.id">{{ fork.title }}</a></p>
                            <p v-if="snippet.forks_quantity == 0"><b>Forks:</b>0</p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.parent"><b>Forked from:</b> <a :href="'/snippets/' + snippet.parent.id">{{ snippet.parent.title }}</a></p>
                            <p v-if="snippet.parent == undefined"><b>Do not have parent fork</b></p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                </div>
            </div>
            <div v-if="snippet_copy" class="column is-9">
                <div class="columns">
                    <div class="column field">
                        <Editor :snippet="snippet_copy" :options="snippet_copy.settings" @code-was-updated="codeWasUpdated"></Editor>
                        <span v-for="error in errors.body" class="title is-6 has-text-danger">{{ error }}</span>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <button class="button is-success is-large is-fullwidth" @click="update()">UPDATE</button>
                    </div>
                </div>
            </div>
            <ring-loader v-else class="column is-narrow"></ring-loader>
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
                Auth: Auth,
                snippet: {},
                snippet_copy: null,
                errors: {
                    description: [],
                    body: [],
                },
                fresh_tags: '',
                show_editor_settings: false
            }
        },
        watch: {
            snippet() {
                if (this.snippet.id) {
                    if (!this.Auth.isOwner(this.snippet)) {
                        return this.$router.push({ name: 'login.create' })
                    }
                    this.snippet_copy = {...this.snippet}
                    this.snippet.tags.map(tag => {
                        this.fresh_tags += tag.name + ', '
                    })
                    this.fresh_tags = this.fresh_tags.substr(this.fresh_tags, this.fresh_tags.length - 2)
                }
            }
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
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet).then(response => {
                response.data.settings = JSON.parse(response.data.settings)
                this.snippet = response.data
            }).catch(error => {
                this.error({message: error.toString()})
                this.$router.push({ name: 'snippets.index' })
            })
        },
        methods:{
            validateForm() {
                if (this.snippet.description.trim().length > 2000) {
                    this.errors.description.push('Description cannot be more then 2000 symbols.')
                }
                if (this.snippet.body.length < 1) {
                    this.errors.body.push('Snippet is required.')
                }
                if (this.snippet.body.length > 100000) {
                    this.errors.body.push('Snippet cannot be more then 100 000 symbols.')
                }

                return (this.errors.description.length + this.errors.body.length) < 1
            },
            resetErrors() {
                this.errors.description = []
                this.errors.body = []
            },
            show(snippet) {
                this.$router.push({ name: 'snippets.show', params: { snippet: snippet.id }})
            },
            update() {
                this.resetErrors()
                if (this.validateForm()) {
                    let response = axios.post('/api/snippets/' + this.snippet_copy.id + '/?api_token=' + this.Auth.getApiToken(), {
                        title: this.snippet.title,
                        description: this.snippet_copy.description,
                        body: this.snippet_copy.body,
                        settings: JSON.stringify(this.snippet_copy.settings),
                        _method: 'PUT'
                    }).then(response => {
                        return response
                    }).catch(error => {
                        this.error({message: error.toString()})
                    })
                    response.then(response => {
                        this.success({message: 'Snippet was updated successful.'})
                        if (this.snippet.tags.length) {
                            this.snippet.tags.map(tag => {
                                axios.post(`/api/tags/${tag.id}/?api_token=` + this.Auth.getApiToken(), {
                                    snippet: this.snippet.id,
                                    _method: 'DELETE'
                                })
                            })
                        }
                        let snippet_id = response.data.id
                        if (this.tags.length) {
                            this.tags.map(tag => {
                                axios.post(`/api/tags?api_token=` + this.Auth.getApiToken(), {
                                    name: tag,
                                    snippet: snippet_id
                                }).then(inner_response => {
                                    this.success({message: 'tags was updated.'})
                                }).catch(inner_error => {
                                    this.error({message: inner_error.toString()})
                                })
                            })
                        }
                        this.$router.push({ name: 'snippets.show', params: {snippet: snippet_id} })
                    })
                }
            },
            destroy(snippet) {
                Event.$emit('show-message', {
                    message: 'Do you confirm deletion?',
                    type: 'warning',
                    callback: () => {
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.Auth.getApiToken(), {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$router.push({ name: 'snippets.index' })
                            this.success({message: 'Snippet was successful deleted. Also all of his tag and fans.'})
                        }).catch(error => {
                            this.error({message: error.toString()})
                        })
                    }
                })
            },
            updateEditorOptions(options) {
                this.show_editor_settings = false
                this.updateStylesheet(options.theme)
                this.snippet_copy.settings = options
            },
            updateStylesheet(name) {
                const current_theme_name = this.snippet_copy.settings.theme
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
                this.snippet_copy.body = code
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
