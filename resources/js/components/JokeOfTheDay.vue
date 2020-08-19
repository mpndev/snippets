<style>
    .most-copied-snippets-ring {
        min-height: 12.5rem
    }
</style>

<template>
    <div v-if="joke.single.length || joke.double.first.length">
        <p class="menu-label title is-6">
            {{ $t('Joke of the day') }}:
        </p>
        <ul class="menu-list has-background-grey-lighter has-padding-10">
            <li><p class="has-text-info">{{joke.single}}</p></li>
            <li><p class="has-text-info">{{joke.double.first}}</p></li>
            <li><p class="has-text-info">{{joke.double.second}}</p></li>
        </ul>
    </div>
    <ring-loader v-else ring-loader class="column most-copied-snippets-ring is-narrow"></ring-loader>
</template>

<script>
    export default {
        props: ['joke']
    }
</script>
