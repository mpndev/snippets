<template>
    <div v-if="paginated_data.total > 5" class="box paginator">
        <div class="columns">
            <div class="column is-3 has-text-centered is-hidden-desktop">
                <span class="tag is-light has-text-info is-unselectable">
                    {{ paginated_data.from }} - {{ paginated_data.to }} / {{ paginated_data.total }} (PAGE-{{ paginated_data.current_page }})
                </span>
            </div>
            <div class="column is-1 has-text-centered is-hidden-mobile">
                <span class="tag is-light is-large has-text-info is-unselectable">{{ paginated_data.from }} - {{ paginated_data.to }} / {{ paginated_data.total }}</span>
            </div>
            <div class="column is-2 is-offset-4 has-text-centered is-hidden-mobile">
                <span class="tag title is-5 is-light has-text-info is-unselectable">~ PAGE-{{ paginated_data.current_page }} ~</span>
            </div>
            <div class="column is-5">
                <nav class="pagination is-right" role="navigation" aria-label="pagination">
                    <ul class="pagination-list">
                        <li v-if="paginated_data.current_page > 2">
                            <span class="pagination-link has-cursor-pointer is-hidden-desktop" aria-label="first page" @click="changePage(1)">1</span>
                        </li>
                        <li v-if="paginated_data.current_page > 2">
                            <span class="pagination-link has-cursor-pointer is-hidden-mobile" aria-label="first page" @click="changePage(1)">first page</span>
                        </li>
                        <li v-if="paginated_data.current_page > 2">
                            <span class="pagination-link has-cursor-pointer is-hidden-mobile" @click="changePage(paginated_data.current_page - 1)">previous page</span>
                        </li>
                        <li v-if="paginated_data.current_page > 2">
                            <span class="pagination-link has-cursor-pointer is-hidden-mobile" :aria-label="paginated_data.current_page - 1" @click="changePage(paginated_data.current_page - 2)">{{ paginated_data.current_page - 2 }}</span>
                        </li>

                        <li v-if="paginated_data.current_page > 1">
                            <span class="pagination-link has-cursor-pointer" :aria-label="paginated_data.current_page - 1" @click="changePage(paginated_data.current_page - 1)">{{ paginated_data.current_page - 1 }}</span>
                        </li>

                        <li>
                            <span class="pagination-link has-cursor-pointer is-current" :aria-label="'Page ' + paginated_data.current_page" aria-current="page" @click="changePage(paginated_data.current_page)">{{ paginated_data.current_page }}</span>
                        </li>

                        <li v-if="paginated_data.current_page < paginated_data.last_page">
                            <span class="pagination-link has-cursor-pointer" :aria-label="paginated_data.current_page + 1" @click="changePage(paginated_data.current_page + 1)">{{ paginated_data.current_page + 1 }}</span>
                        </li>

                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span class="pagination-link has-cursor-pointer is-hidden-mobile" :aria-label="paginated_data.current_page + 2" @click="changePage(paginated_data.current_page + 2)">{{ paginated_data.current_page + 2 }}</span>
                        </li>
                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span class="pagination-link has-cursor-pointer is-hidden-mobile" :area-label="'next page'" @click="changePage(paginated_data.current_page + 1)">next page</span>
                        </li>
                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span class="pagination-link has-cursor-pointer is-hidden-mobile" aria-label="last page" @click="changePage(paginated_data.last_page)">last page</span>
                        </li>
                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span class="pagination-link has-cursor-pointer is-hidden-desktop" aria-label="last page" @click="changePage(paginated_data.last_page)">{{ paginated_data.last_page }}</span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['paginated_data'],
        methods: {
            changePage(to) {
                let query = {...this.$route.query}
                query.page = to
                this.$router.replace({ name: 'snippets.index', query: query}).catch(e => {})
            }
        }
    }
</script>
