<style>
    .most-copied-snippets-ring {
        min-height: 12.5rem
    }
</style>

<template>
    <aside v-if="most_copied_snippets.length" class="menu">
        <p :class="{ 'darkmod': Auth.isDarkMod() }" class="menu-label title is-6 text-is-dark">
            {{ $t('Top 5 copied snippets') }}:
        </p>
        <ul class="menu-list">
            <li v-for="snippet in most_copied_snippets">
                <a :class="{ 'darkmod': Auth.isDarkMod() }" class="title is-7 text-is-dark" :href="`/snippets/${snippet.slug}`">
                    <span class="tag is-warning is-inline"><span class="fa fa-clipboard has-text-success"></span> X {{ snippet.times_copied }}</span>
                    {{ snippet.title }}
                </a>
            </li>
        </ul>
    </aside>
    <ring-loader v-else ring-loader class="column most-copied-snippets-ring is-narrow"></ring-loader>
</template>

<script>
    export default {
        props: ['most_copied_snippets'],
        data: () => {
            return {
                Auth: Auth
            }
        }
    }
</script>
