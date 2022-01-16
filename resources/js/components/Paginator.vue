<template>
    <div v-if="paginated_data.total > 5" :class="{ 'darkmod': Auth.isDarkMod() }" class="box paginator background-is-white">
        <div class="columns">
            <div class="column is-3 has-text-centered is-hidden-desktop">
                <span :class="{ 'darkmod': Auth.isDarkMod() }" class="tag is-light background-is-grey-lighter text-is-grey-black-bis is-unselectable">
                    {{ paginated_data.from }} - {{ paginated_data.to }} / {{ paginated_data.total }} ({{ $t('PAGE') }}-{{ paginated_data.current_page }})
                </span>
            </div>
            <div class="column is-1 has-text-centered is-hidden-mobile">
                <span :class="{ 'darkmod': Auth.isDarkMod() }" class="tag is-light is-large background-is-grey-lighter text-is-grey-black-bis is-unselectable">{{ paginated_data.from }} - {{ paginated_data.to }} / {{ paginated_data.total }}</span>
            </div>
            <div class="column is-2 is-offset-4 has-text-centered is-hidden-mobile">
                <span :class="{ 'darkmod': Auth.isDarkMod() }" class="tag title is-5 is-light background-is-grey-lighter text-is-grey-black-bis is-unselectable">~ {{ $t('PAGE') }}-{{ paginated_data.current_page }} ~</span>
            </div>
            <div class="column is-5">
                <nav class="pagination is-right" role="navigation" aria-label="pagination">
                    <ul class="pagination-list">
                        <li v-if="paginated_data.current_page > 2">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-desktop background-is-grey-lighter text-is-grey-black-bis" aria-label="first page" @click="changePage(1)">1</span>
                        </li>
                        <li v-if="paginated_data.current_page > 2">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-mobile background-is-grey-lighter text-is-grey-black-bis" aria-label="first page" @click="changePage(1)">{{ $t('first page') }}</span>
                        </li>
                        <li v-if="paginated_data.current_page > 2">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-mobile background-is-grey-lighter text-is-grey-black-bis" @click="changePage(paginated_data.current_page - 1)">‹</span>
                        </li>
                        <li v-if="paginated_data.current_page > 2">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-mobile background-is-grey-lighter text-is-grey-black-bis" :aria-label="paginated_data.current_page - 1" @click="changePage(paginated_data.current_page - 2)">{{ paginated_data.current_page - 2 }}</span>
                        </li>

                        <li v-if="paginated_data.current_page > 1">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer background-is-grey-lighter text-is-grey-black-bis" :aria-label="paginated_data.current_page - 1" @click="changePage(paginated_data.current_page - 1)">{{ paginated_data.current_page - 1 }}</span>
                        </li>

                        <li>
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-current background-is-grey-lighter text-is-grey-black-bis" :aria-label="'Page ' + paginated_data.current_page" aria-current="page" @click="changePage(paginated_data.current_page)">{{ paginated_data.current_page }}</span>
                        </li>

                        <li v-if="paginated_data.current_page < paginated_data.last_page">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer background-is-grey-lighter text-is-grey-black-bis" :aria-label="paginated_data.current_page + 1" @click="changePage(paginated_data.current_page + 1)">{{ paginated_data.current_page + 1 }}</span>
                        </li>

                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-mobile background-is-grey-lighter text-is-grey-black-bis" :aria-label="paginated_data.current_page + 2" @click="changePage(paginated_data.current_page + 2)">{{ paginated_data.current_page + 2 }}</span>
                        </li>
                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-mobile background-is-grey-lighter text-is-grey-black-bis" :area-label="'next page'" @click="changePage(paginated_data.current_page + 1)">›</span>
                        </li>
                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-mobile background-is-grey-lighter text-is-grey-black-bis" aria-label="last page" @click="changePage(paginated_data.last_page)">{{ $t('last page') }}</span>
                        </li>
                        <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                            <span :class="{ 'darkmod': Auth.isDarkMod() }" class="pagination-link has-cursor-pointer is-hidden-desktop background-is-grey-lighter text-is-grey-black-bis" aria-label="last page" @click="changePage(paginated_data.last_page)">{{ paginated_data.last_page }}</span>
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
        data: () => {
            return {
                Auth: Auth
            }
        },
        methods: {
            changePage(to) {
                let query = {...this.$route.query}
                query.page = to
                this.$router.replace({ name: 'snippets.index', query: query}).catch(e => {})
            }
        }
    }
</script>
