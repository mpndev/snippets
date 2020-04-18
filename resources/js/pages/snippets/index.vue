<style>
    .snippet-summary-ring {
        min-height: 140px;
    }
    .paginator-ring {
        height: 64px;
    }
    .most-liked-snippets, .most-copied-snippets, .joke-of-the-day {
        min-height: 276px;
    }
    .joke-of-the-day {
        overflow: scroll;
    }
</style>

<template>
    <div>
        <search></search>

        <paginator v-if="has_results" :paginated_data="paginated_data"></paginator>
        <div v-if="show_rings" class="box">
            <div class="columns is-centered paginator-ring">
                <ring-loader class="column is-narrow"></ring-loader>
            </div>
        </div>

        <div class="columns">
            <div class="column is-2  is-hidden-mobile">
                <div class="box most-liked-snippets">
                    <most-liked-snippets :most_liked_snippets="most_liked_snippets"></most-liked-snippets>
                </div>
                <div class="box most-copied-snippets">
                    <most-copied-snippets :most_copied_snippets="most_copied_snippets"></most-copied-snippets>
                </div>
                <div class="box joke-of-the-day">
                    <joke-of-the-day :joke="joke"></joke-of-the-day>
                </div>
            </div>
            <div class="column">
                <div>
                    <div v-if="has_results" v-for="snippet in paginated_data.data" class="box">
                        <snippet-summary :key="snippet.id" :snippet="snippet" @snippet-was-deleted="snippetWasDeleted" @favorite-was-changed="updateMostLikedSnippets" @snippet-was-copied="updateMostCopiedSnippets"></snippet-summary>
                    </div>
                    <div v-if="!has_results" class="columns is-centered">
                        <div v-if="!show_rings" class="column">
                            <div class="colun"></div>
                            <p class="title has-text-centered is-3">No results found.</p>
                        </div>
                    </div>
                    <div v-if="show_rings" v-for="i in 5" class="box">
                        <div class="columns is-centered">
                            <ring-loader class="column is-narrow snippet-summary-ring"></ring-loader>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <paginator v-if="has_results" :paginated_data="paginated_data"></paginator>
        <div v-if="show_rings" class="box">
            <div class="columns is-centered paginator-ring">
                <ring-loader class="column is-narrow"></ring-loader>
            </div>
        </div>

        <div class="columns is-hidden-desktop">
            <div class="column">
                <div class="box most-liked-snippets">
                    <most-liked-snippets :most_liked_snippets="most_liked_snippets"></most-liked-snippets>
                </div>
                <div class="box most-copied-snippets">
                    <most-copied-snippets :most_copied_snippets="most_copied_snippets"></most-copied-snippets>
                </div>
                <div class="box joke-of-the-day">
                    <joke-of-the-day :joke="joke"></joke-of-the-day>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Paginator from './../../components/Paginator'
    import SnippetSummary from './../../components/SnippetSummary'
    import Search from "./../../components/Search"
    import MostLikedSnippets from "./../../components/MostLikedSnippets"
    import MostCopiedSnippets from "./../../components/MostCopiedSnippets"
    import JokeOfTheDay from "./../../components/JokeOfTheDay"

    export default {
        components: {
            SnippetSummary: SnippetSummary,
            Paginator: Paginator,
            search: Search,
            MostLikedSnippets: MostLikedSnippets,
            MostCopiedSnippets: MostCopiedSnippets,
            JokeOfTheDay: JokeOfTheDay
        },
        data: () => {
            return {
                Auth: Auth,
                paginated_data: {
                    data: []
                },
                most_liked_snippets: [],
                most_copied_snippets: [],
                has_results: false,
                show_rings: true,
                joke: {
                    single: '',
                    double: {
                        first: '',
                        second: ''
                    }
                }
            }
        },
        mounted() {

            axios.get('https://sv443.net/jokeapi/category/programming').then(response => {
                if (response.data.type == 'single') {
                    this.joke.single = response.data.joke
                }
                else if (response.data.type == 'twopart') {
                    this.joke.double.first = response.data.setup
                    this.joke.double.second = response.data.delivery
                }
            })

            let query = this.$router.currentRoute.fullPath.substr(this.$router.currentRoute.path)
            let api_token_param = ''
            if (this.Auth.check()) {
                api_token_param = 'api_token=' + this.Auth.getApiToken()
                if (query.length > 1) {
                    query = query + '&' + api_token_param
                }
                else {
                    query = '/?' + api_token_param
                }
            }
            axios.get('/api/snippets' + query).then(response => {
                this.paginated_data = response.data
                if (this.paginated_data.data.length) {
                    this.has_results = true
                }
                this.show_rings = false
            }).catch(error => {
                this.error({message: error.toString()})
            })
            axios('/api/snippets?limit=5&most-liked-snippets=true').then(response => {
                this.most_liked_snippets = response.data.data
            })
            axios('/api/snippets?limit=5&most-copied-snippets=true').then(response => {
                this.most_copied_snippets = response.data.data
            })
        },
        methods: {
            snippetWasDeleted(id) {
                if (this.paginated_data.data.length > 1) {
                    this.$router.go(0)
                }
                else {
                    this.$router.push({ name: 'snippets.index' })
                }
            },
            updateMostLikedSnippets(snippet) {
                const must_update_liked_snippets = this.most_liked_snippets.some(liked_snippet => {
                    return (liked_snippet.id === snippet.id || liked_snippet.fans_quantity < snippet.fans_quantity)
                })
                if (must_update_liked_snippets) {
                    this.most_liked_snippets = []
                    setTimeout(() => {
                        axios('/api/snippets?limit=5&most-liked-snippets=true').then(response => {
                            this.most_liked_snippets = response.data.data
                        })
                    }, 500)
                }
            },
            updateMostCopiedSnippets(snippet) {
                this.paginated_data.data.map(original_snippet => {
                    if (original_snippet.id === snippet.id) {
                        original_snippet.times_copied = snippet.times_copied;
                    }
                })

                const must_update_copied_snippets = this.most_copied_snippets.some(copied_snippet => {
                    return (copied_snippet.id === snippet.id || copied_snippet.times_copied < snippet.times_copied)
                })

                if (must_update_copied_snippets) {
                    setTimeout(() => {
                        this.most_copied_snippets = []
                        axios('/api/snippets?limit=5&most-copied-snippets=true').then(response => {
                            this.most_copied_snippets = response.data.data
                        })
                    }, 500)
                }
            }
        },
        notifications: require('../../GlobalNotifications')
    }
    // компонент за бутоните
    // форм модел
    // сниппет модел
</script>
