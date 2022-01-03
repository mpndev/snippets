<template>
    <div>
        <editor-settings v-if="snippet_copy" :show_editor_settings="show_editor_settings" @editor-options-was-updated="updateEditorOptions"></editor-settings>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div v-if="snippet.id">
                        <div class="columns" >
                            <div class="column is-three-fifths" v-if="Auth.check()">
                                <button class="button is-success fa fa-clipboard" :title="$t('copy code to the clipboard')"></button>
                                <button class="button is-info fa fa-eye" :title="$t('show the snippet')" @click="show(snippet)"></button>
                                <button v-if="Auth.check() && Auth.isOwner(snippet)" class="button is-danger fa fa-trash-alt" :title="$t('delete the snippet')" @click="destroy(snippet)"></button>
                            </div>
                            <ring-loader v-else class="is-narrow"></ring-loader>
                            <div v-if="!snippet_copy.public && Auth.check()" class="column">
                                <button class="button is-danger fa fa-lock" @click="snippet_copy.public = !snippet_copy.public" :title="$t('This snippet is visible only to you!')"></button>
                                <button class="button is-info fa fa-cog" @click="show_editor_settings = true"></button>
                            </div>
                            <div v-if="snippet_copy.public && Auth.check()" class="column">
                                <button class="button is-warning fa fa-unlock" @click="snippet_copy.public = !snippet_copy.public" :title="$t('This snippet is visible to everyone!')"></button>
                                <button class="button is-info fa fa-cog" @click="show_editor_settings = true"></button>
                            </div>
                        </div>
                    </div>
                    <ring-loader v-else class="is-narrow"></ring-loader>
                    <hr>
                    <div>
                        <p v-if="snippet.user"><b>{{ $t('Author') }}:</b> {{ snippet.user.name }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
                            <div v-if="snippet.id" class="control">
                                <label for="title">{{ $t('Title') }}:</label>
                                <input class="input" id="title" type="text" :placeholder="$t('min symbols 1, max symbols 255')" v-model="snippet_copy.title">
                                <span class="tag is-info is-light is-small is-rounded is-pulled-right">{{ max_title_length - snippet_copy.title.length }}</span>
                                <span v-for="error in errors.title" class="title is-6 has-text-danger">{{ error }}</span>
                            </div>
                            <ring-loader v-else class="is-narrow"></ring-loader>
                        </div>
                    </div>
                    <hr>
                    <div class="field">
                        <div v-if="snippet.id" class="control">
                            <label for="description">{{ $t('Description') }}:</label>
                            <input class="input" id="description" type="text" :placeholder="$t('max symbols 2000')" v-model="snippet_copy.description">
                            <span class="tag is-info is-light is-small is-rounded is-pulled-right">{{ max_description_length - snippet_copy.description.length }}</span>
                            <span v-for="error in errors.description" class="title is-6 has-text-danger">{{ error }}</span>
                        </div>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>{{ $t('Created') }}: </b> {{ snippet.created_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>{{ $t('Last update') }}: </b> {{ snippet.updated_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id" class="field">
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
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.forks_quantity > 0" v-for="fork in snippet.forks"><b>{{ $t('Fork') }}:</b> <a :href="'/snippets/' + fork.id">{{ fork.title }}</a></p>
                            <p v-if="snippet.forks_quantity == 0"><b>{{ $t('Forks') }}:</b>0</p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.parent"><b>{{ $t('Forked from') }}:</b> <a :href="'/snippets/' + snippet.parent.slug">{{ snippet.parent.title }}</a></p>
                            <p v-if="snippet.parent == undefined"><b>{{ $t('Do not have parent fork') }}</b></p>
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
                    <div class="column is-11">
                        <button class="button is-success is-large is-fullwidth" @click="update()">{{ $t('UPDATE') }}</button>
                    </div>
                    <div class="column is-1">
                        <span class="tag is-info is-light is-medium is-rounded">{{ max_body_length - snippet_copy.body.length }}</span>
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
                min_title_length: 1,
                max_title_length: 255,
                max_body_length: 100000,
                max_description_length: 2000,
                Auth: Auth,
                snippet: {},
                snippet_copy: null,
                errors: {
                    title: [],
                    description: [],
                    body: [],
                },
                fresh_tags: '',
                show_editor_settings: false
            }
        },
        created() {
            this.warn({message: this.$t('Do not store sensitive data like passwords, tokens, etc...')})
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
            document.querySelector('title').innerHTML = this.$t('edit the snippet')
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet_id_or_slug + '?api_token=' + (this.Auth.check() ? this.Auth.getApiToken() : '')).then(response => {
                response.data.settings = JSON.parse(response.data.settings)
                this.snippet = response.data
            }).catch(error => {
                this.error({message: error.toString()})
                this.$router.push({ name: 'snippets.index' })
            })
        },
        methods:{
            validateForm() {
                if (this.snippet.title.trim().length < this.min_title_length) {
                    this.errors.title.push(this.$t('Title is required.'))
                }
                if (this.snippet.title.trim().length > this.max_title_length) {
                    this.errors.title.push(this.$t('Title cannot be more then 255 symbols.'))
                }
                if (this.snippet.description.trim().length > this.max_description_length) {
                    this.errors.description.push(this.$t('Description cannot be more then 2000 symbols.'))
                }
                if (this.snippet.body.length < 1) {
                    this.errors.body.push(this.$t('Snippet is required.'))
                }
                if (this.snippet.body.length > this.max_body_length) {
                    this.errors.body.push(this.$t('Snippet cannot be more then 100 000 symbols.'))
                }

                return (this.errors.title.length + this.errors.description.length + this.errors.body.length) < 1
            },
            resetErrors() {
                this.errors.title = []
                this.errors.description = []
                this.errors.body = []
            },
            show(snippet) {
                this.$router.push({ name: 'snippets.show', params: { snippet_id_or_slug: snippet.slug }})
            },
            update() {
                this.resetErrors()
                if (this.validateForm()) {
                    const response = axios.post('/api/snippets/' + this.snippet.slug + '?api_token=' + this.Auth.getApiToken(), {
                        title: this.snippet_copy.title,
                        description: this.snippet_copy.description,
                        body: this.snippet_copy.body,
                        settings: JSON.stringify(this.snippet_copy.settings),
                        public: this.snippet_copy.public,
                        _method: 'PUT'
                    }).then(response => {
                        return response
                    }).catch(error => {
                        if (error.response.data.title) {
                            this.errors.title.push(this.$t('Title has already been taken'))
                            return
                        }
                        this.error({message: error.toString()})
                    })
                    response.then(response => {
                        if (response && response.status == 200) {
                            const slug = response.data.slug
                            this.success({message: this.$t('Snippet was updated successful.')})
                            if (response.data.tags.length) {
                                this._deleteTags(response)
                            } else if (this.tags.length) {
                                this._createTags(response)
                            } else {
                                this.$router.push({ name: 'snippets.show', params: {snippet_id_or_slug: slug} })
                            }
                        }
                    })
                }
            },
            _deleteTags(response) {
                const deleteTagsRequests = []
                const slug = response.data.slug
                this.snippet.tags.map(tag => {
                    deleteTagsRequests.push(
                        axios.post(`/api/tags/${tag.id}?api_token=` + this.Auth.getApiToken(), {
                            snippet_id_or_slug: slug,
                            _method: 'DELETE'
                        })
                    )
                })
                axios.all(deleteTagsRequests).then(() => {
                    if (this.tags.length) {
                        this._createTags(response)
                    } else {
                        this.$router.push({ name: 'snippets.show', params: {snippet_id_or_slug: slug} })
                    }
                })
            },
            _createTags(response) {
                const createdTagsRequests = []
                const slug = response.data.slug
                this.tags.map(tag => {
                    createdTagsRequests.push(
                        axios.post(`/api/tags?api_token=` + this.Auth.getApiToken(), {
                            name: tag,
                            snippet_id_or_slug: slug
                        }).then(inner_response => {
                            this.success({message: this.$t('Tags was updated.')})
                        }).catch(inner_error => {
                            this.error({message: inner_error.toString()})
                        })
                    )
                })
                axios.all(createdTagsRequests).then(() => {
                    this.$router.push({ name: 'snippets.show', params: {snippet_id_or_slug: slug} })
                })
            },
            destroy(snippet) {
                Event.$emit('show-message', {
                    message: this.$t('Do you confirm deletion?'),
                    type: 'warning',
                    callback: () => {
                        axios.post('/api/snippets/' + snippet.slug + '?api_token=' + this.Auth.getApiToken(), {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$router.push({ name: 'snippets.index' })
                            this.success({message: this.$t('Snippet was successful deleted. Also all of his tag and fans.')})
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
