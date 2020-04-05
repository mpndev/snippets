<template>
    <div>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div>
                        <p v-if="user"><b>Author:</b> {{ user.name }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
                            <div class="control">
                                <label for="title">Title:</label>
                                <input class="input" id="title" type="text" placeholder="min symbols 1, max symbols 255" v-model="snippet.title">
                                <span v-for="error in errors.title" class="title is-6 has-text-danger">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
                            <div class="control">
                                <label for="description">Description:</label>
                                <input class="input" id="description" type="text" placeholder="max symbols 2000" v-model="snippet.description">
                                <span v-for="error in errors.description" class="title is-6 has-text-danger">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="field">
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
                    </div>
                </div>
            </div>
            <div class="column is-9">
                <div class="columns">
                    <p class="column field">
                        <textarea class="textarea" id="body" cols="30" rows="25" placeholder="min symbols 1, max symbols 100 000" v-model="snippet.body"></textarea>
                        <span v-for="error in errors.body" class="title is-6 has-text-danger">{{ error }}</span>
                    </p>
                </div>
                <div class="columns">
                    <div class="column">
                        <button class="button is-success is-large is-fullwidth" @click="create()">CREATE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                user: null,
                snippet: {
                    title: '',
                    description: '',
                    body: '',
                },
                parent: {},
                fresh_tags: '',
                errors: {
                    title: [],
                    description: [],
                    body: [],
                }
            }
        },
        created() {
            if (!this.$root.user) {
                this.$router.push({ name: 'login.create' })
            }
            else {
                this.user = this.$root.user
            }
        },
        mounted() {
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet).then(response => {
                this.parent = response.data
            }).catch(error => {
                this.$router.push({ name: 'snippets.index' })
                this.failToLoadParent({
                    message: error.toString()
                })
            })
        },
        watch: {
            parent() {
                if (this.parent.id) {
                    this.snippet = {...this.parent}
                }
                this.parent.tags.map(tag => {
                    this.fresh_tags += tag.name + ', '
                })
                this.fresh_tags = this.fresh_tags.substr(this.fresh_tags, this.fresh_tags.length - 2)
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
        methods: {
            validateForm() {
                if (this.snippet.title.trim().length < 1) {
                    this.errors.title.push('Title is required.')
                }
                if (this.snippet.title.trim().length > 255) {
                    this.errors.title.push('Title cannot be more then 255 symbols.')
                }
                if (this.snippet.title == this.parent.title) {
                    this.errors.title.push('Fork title cannot be the same as the parent snippet title.')
                }
                if (this.snippet.description.trim().length > 2000) {
                    this.errors.description.push('Description cannot be more then 2000 symbols.')
                }
                if (this.snippet.body.length < 1) {
                    this.errors.body.push('Snippet is required.')
                }
                if (this.snippet.body.length > 100000) {
                    this.errors.body.push('Snippet cannot be more then 100 000 symbols.')
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
                    let response = axios.post('/api/snippets/?api_token=' + this.user.api_token, {
                        title: this.snippet.title,
                        description: this.snippet.description,
                        body: this.snippet.body,
                        _parent_id: this.parent.id
                    }).then(response => {
                        return response
                    }).catch(error => {
                        this.failToStoreFork({
                            message: error.toString()
                        })
                    })
                    response.then(response => {
                        let snippet_id = response.data.id
                        this.successfulStoreFork({
                            message: 'Fork was created successful.'
                        })
                        if (this.tags.length) {
                            this.tags.map(tag => {
                                axios.post(`/api/tags?api_token=` + this.user.api_token, {
                                    name: tag,
                                    snippet: snippet_id
                                }).then(inner_response => {
                                    this.successfulStoreTag({
                                        message: `"${inner_response.data.name}" tag was added to the snippet.`
                                    })
                                }).catch(inner_error => {
                                    this.failToStoreTag({
                                        message: inner_error.toString()
                                    })
                                })
                            })
                        }
                        this.$router.push({ name: 'snippets.show', params: {snippet: snippet_id} })
                    })
                }
            }
        },
        notifications: {
            failToLoadParent: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulStoreFork: {
                title: 'Success!',
                message: '',
                type: 'success'
            },
            failToStoreFork: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulStoreTag: {
                title: 'Success!',
                message: '',
                type: 'success'
            },
            failToStoreTag: {
                title: 'Error!',
                message: '',
                type: 'error'
            }
        }
    }
</script>
