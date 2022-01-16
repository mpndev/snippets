<template>
    <div :class="{ 'darkmod': Auth.isDarkMod() }" class="box background-is-white">
        <div class="columns">
            <div class="column is-4">
                <div class="tags">
                    <span v-if="Auth.check()" class="tag is-rounded has-cursor-pointer is-unselectable" :class="{'is-info': my_snippets}" @click="toggleFilter('my_snippets')">{{ $t('my snippets') }}</span>
                    <span v-if="Auth.check()" class="tag is-rounded has-cursor-pointer is-unselectable" :class="{'is-info': private_snippets}" @click="toggleFilter('private_snippets')">{{ $t('my private snippets') }}</span>
                    <span v-if="Auth.check()" class="tag is-rounded has-cursor-pointer is-unselectable" :class="{'is-info': favorite_snippets}" @click="toggleFilter('favorite_snippets')">{{ $t('my favorite snippets') }}</span>
                    <span v-if="Auth.check()" class="tag is-rounded has-cursor-pointer is-unselectable" :class="{'is-info': forked_snippets}" @click="toggleFilter('forked_snippets')">{{ $t('my snippets that was extended') }}</span>
                    <span v-if="Auth.check()" class="tag is-rounded has-cursor-pointer is-unselectable" :class="{'is-info': forks_snippets}" @click="toggleFilter('forks_snippets')">{{ $t('snippets that extends my snippets') }}</span>
                    <span class="tag is-rounded has-cursor-pointer is-unselectable" :class="{'is-info': latest}" @click="toggleFilter('latest')">{{ $t('latest') }}</span>
                    <span v-if="show_reset_filters_button" class="tag is-rounded has-cursor-pointer is-unselectable is-warning" @click="resetFilters">{{ $t('reset filters') }}</span>
                </div>
            </div>
            <div class="column is-4">
                <div class="field">
                    <label>
                        <textarea id="tags" class="textarea" cols="30" rows="2" :placeholder="$t('Filter by tags. Separate them with comma: js, bash, drupal 8, php')" v-model="tags" @keyup="debauncedTagsSearch"></textarea>
                    </label>
                </div>
            </div>
            <div class="column is-3 is-offset-1 field has-addon">
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <input id="search" class="input is-rounded" type="text" :placeholder="$t('looking for...')" v-model="search" @keyup="debauncedSearch" @keyup.enter="searchRun('search')">
                    </div>
                </div>
                <p class="title is-7 has-text-info is-unselectable">{{search_button_text}}</p>
            </div>
        </div>
    </div>
</template>

<script>
    import _ from "lodash"
    export default {
        data: () => {
            return {
                Auth: Auth,
                page: 1,
                my_snippets: false,
                private_snippets: false,
                favorite_snippets: false,
                forked_snippets: false,
                forks_snippets: false,
                search: '',
                tags: '',
                latest: false
            }
        },
        mounted() {
            if (Initializer === 'search' || Initializer === 'tags') {
                document.getElementById(Initializer).focus()
                Initializer = null
            }
            let query = {...this.$route.query}
            this.page = 1
            this.my_snippets = query['my-snippets'] ? query['my-snippets'] : this.my_snippets
            this.private_snippets = query['my-private-snippets'] ? query['my-private-snippets'] : this.private_snippets
            this.favorite_snippets = query['my-favorite-snippets'] ? query['my-favorite-snippets'] : this.favorite_snippets
            this.forked_snippets = query['my-forked-snippets'] ? query['my-forked-snippets'] : this.forked_snippets
            this.forks_snippets = query['forks-of-my-snippets'] ? query['forks-of-my-snippets'] : this.forks_snippets
            this.search = query['search'] ? query['search'] : this.search
            this.tags = query['with-tags'] ? query['with-tags'] : this.tags
            this.latest = query['latest'] ? query['latest'] : this.latest
            this.snippets_by_author = query['snippets-by-author'] ? query['snippets-by-author'] : false
            this.snippets_by_day = query['snippets-created-at-the-same-day-as'] ? query['snippets-created-at-the-same-day-as'] : false
        },
        computed: {
            show_reset_filters_button() {
                let reset_it = false
                if (Object.keys(this.$route.query).length > 1) {
                    reset_it = true
                }
                if (Object.keys(this.$route.query).length === 1 && this.$route.query.page === undefined) {
                    reset_it = true
                }
                return reset_it
            },
            query() {
                let query = {}
                let params = {
                    'my-snippets': this.my_snippets,
                    'my-private-snippets': this.private_snippets,
                    'my-favorite-snippets': this.favorite_snippets,
                    'my-forked-snippets': this.forked_snippets,
                    'forks-of-my-snippets': this.forks_snippets,
                    'search': this.search,
                    'page': 1,
                    'with-tags': this.tags,
                    'latest': this.latest,
                    'snippets-by-author': this.snippets_by_author,
                    'snippets-created-at-the-same-day-as': this.snippets_by_day
                }

                for(const param in params) {
                    if (typeof params[param] === "boolean") {
                        if (params[param]) {
                            query[param] = true
                        }
                    }
                    else if (param == 'with-tags') {
                        let tags = params[param]
                        if (tags.length) {
                            query[param] = tags
                            this.tags = tags
                        }
                    }
                    else if (typeof params[param] === "number") {
                        query[param] = params[param]
                    }
                    else {
                        if (params[param].length) {
                            query[param] = params[param]
                        }
                    }
                }

                return query
            },
            search_button_text() {
                let text = ''
                let is_first = true
                let options = [
                    this.my_snippets ? this.$t(' was created from you') : '',
                    this.private_snippets ? this.$t('are private') : '',
                    this.favorite_snippets ? this.$t('you like') : '',
                    this.forked_snippets ? this.$t('have forks') : '',
                    this.forks_snippets ? this.$t('extends my snippets') : '',
                    this.search ? `${this.$t('contains string')} - "${this.search}" ${this.$t('in title, body or description')}` : '',
                    this.tags ? `${this.$t('have tags')}: "${this.tags}"` : '',
                    this.latest ? this.$t(' are ordered by creation time') : '',
                    this.snippets_by_author ? this.$t(' belongs to specific author') : '',
                    this.snippets_by_day ? this.$t(' are created on specific day') : ''
                ]
                if (options.some(option => option.length > 0)) {
                    text += this.$t('Looking for snipiites that ')
                    options.map(option => {
                        if (option.length) {
                            text += (!is_first ? this.$t(' and ') : '')+option
                            is_first = false
                        }
                    })
                    text += '.'
                }

                return text
            }
        },
        methods: {
            resetFilters() {
                this.$router.push({ name: 'snippets.index' })
            },
            debauncedSearch: _.debounce(function() {
                if (this.search.length) {
                    this.searchRun('search')
                }
            }, 1500),
            debauncedTagsSearch: _.debounce(function() {
                if (this.tags.length) {
                    this.searchRun('tags')
                }
            }, 1500),
            searchRun(initializer) {
                Initializer = initializer
                this.$router.push({ name: 'snippets.index', query: this.query }).catch(e => {})
            },
            toggleFilter(type) {
                this[type] = !this[type]
                this.searchRun()
            }
        }
    }
</script>
