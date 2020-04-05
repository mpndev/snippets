<template>
    <div>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div v-if="snippet.id">
                        <button class="button is-success fa fa-clipboard" title="copy code to the clipboard"></button>
                        <button class="button is-info fa fa-eye" title="show the snippet" @click="show(snippet)"></button>
                        <button v-if="user && user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" title="delete the snippet" @click="destroy(snippet)"></button>
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
            <div v-if="snippet.id" class="column is-9">
                <div class="columns">
                    <div class="column field">
                        <textarea class="textarea" id="body" cols="30" rows="25" placeholder="min symbols 1, max symbols 100 000" v-model="snippet_copy.body"></textarea>
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
    export default {
        data: () => {
            return {
                snippet: {},
                snippet_copy: null,
                user: null,
                errors: {
                    description: [],
                    body: [],
                },
                fresh_tags: ''
            }
        },
        created() {
            if (this.$root.user) {
                this.user = this.$root.user
            }
            else {
                return this.$router.push({ name: 'login.create' })
            }
        },
        watch: {
            snippet() {
                if (this.snippet != null) {
                    if (this.user.id !== this.snippet.user.id) {
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
                this.snippet = response.data
            }).catch(error => {
                this.failedToLoadParentSnippet({
                    title: 'Error!',
                    message: error.toString()
                })
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
                    let response = axios.post('/api/snippets/' + this.snippet_copy.id + '/?api_token=' + this.user.api_token, {
                        title: this.snippet.title,
                        description: this.snippet_copy.description,
                        body: this.snippet_copy.body,
                        _method: 'PUT'
                    }).then(response => {
                        return response
                    }).catch(error => {
                        this.failedUpdatingSnippet({
                            message: error.toString()
                        })
                    })
                    response.then(response => {
                        this.successfulUpdatingSnippet({
                            message: 'Snippet was updated successful.'
                        })
                        if (this.snippet.tags.length) {
                            this.snippet.tags.map(tag => {
                                axios.post(`/api/tags/${tag.id}/?api_token=` + this.user.api_token, {
                                    snippet: this.snippet.id,
                                    _method: 'DELETE'
                                })
                            })
                        }
                        let snippet_id = response.data.id
                        if (this.tags.length) {
                            this.tags.map(tag => {
                                axios.post(`/api/tags?api_token=` + this.user.api_token, {
                                    name: tag,
                                    snippet: snippet_id
                                }).then(inner_response => {
                                    this.successfulUpdatingTag({
                                        message: 'tags was updated.'
                                    })
                                }).catch(inner_error => {
                                    this.failedUpdatingTag({
                                        message: inner_error.toString()
                                    })
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
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.user.api_token, {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$router.push({ name: 'snippets.index' })
                            this.successfulDeletingSnippet({
                                message: 'Snippet was successful deleted. Also all of his tag and fans.'
                            })
                        }).catch(error => {
                            this.failedDeletingSnippet({
                                message: error.toString()
                            })
                        })
                    }
                })
            }
        },
        notifications: {
            failedToLoadParentSnippet: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulUpdatingSnippet: {
                title: 'Success!',
                message: '',
                type: 'success'
            },
            failedUpdatingSnippet: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulUpdatingTag: {
                title: 'Success!',
                message: '',
                type: 'success'
            },
            failedUpdatingTag: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulDeletingSnippet: {
                title: 'Success!',
                message: '',
                type: 'success'
            },
            failedDeletingSnippet: {
                title: 'Error!',
                message: '',
                type: 'error'
            }
        }
    }
</script>
