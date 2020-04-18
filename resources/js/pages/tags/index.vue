<style>
    .has-columns {
        padding: 1rem;
        column-count: 4;
        column-gap: 40px;
        text-align: center;
    }
    .letter-group {
        margin-bottom: 3rem;
        border-radius: 8px;
    }
    .letter {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        margin-top: 0;
        font-size: 46px;
        text-transform: uppercase;
    }
    .letter.second {
        border-radius: 0 0 8px 8px;
    }
    .is-tag:hover {
        background-color: #00d1b2;
    }
</style>

<template>
<div>
    <div v-if="tagsCollection.length">
        <div class="columns is-centered">
            <div class="column is-3">
                <h2 class="box title is-2 has-text-centered has-text-primary">All available tags</h2>
            </div>
        </div>
        <div class="section">
            <div class="container">
                <div class="has-columns">
                    <div v-for="tags in tagsCollection" class="letter-group has-background-white">
                        <ul>
                            <p class="has-text-centered has-background-dark has-text-primary letter">{{tags[0].name.substr(0, 1)}}</p>
                            <li v-for="tag in tags" class="is-tag title is-6 has-text-info has-cursor-pointer has-padding-10 is-marginless" @click="goToSnippetsWithThisTag(tag)">{{tag.name}}</li>
                            <p class="second has-text-centered has-background-dark has-text-primary letter">{{tags[0].name.substr(0, 1)}}</p>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="box">
        <ring-loader></ring-loader>
    </div>
</div>
</template>

    <script>
export default {
    data: () => {
        return {
            tags: [],
        }
    },
    mounted() {
        axios.get('/api/tags').then(response => {
            this.tags = response.data
        })
    },
    computed: {
        tagsCollection() {
            const tagsCollection = []
            for(let letter in this.tags) {
                tagsCollection.push(this.tags[letter])
            }
            return tagsCollection
        }
    },
    methods: {
        goToSnippetsWithThisTag(tag) {
            this.$router.push({name: 'snippets.index', query: {'with-tags': tag.name}})
        }
    }
}
</script>
