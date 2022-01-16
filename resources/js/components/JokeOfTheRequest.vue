<style>
    .most-copied-snippets-ring {
        min-height: 12.5rem
    }
</style>

<template>
    <div v-if="joke.single.length || joke.double.first.length" class="menu">
        <p :class="{ 'darkmod': Auth.isDarkMod() }" class="menu-label title is-6 text-is-dark">
            {{ $t('Joke of the request') }}:
        </p>
        <ul class="menu-list has-padding-10">
            <li><p class="has-text-info">{{joke.single}}</p></li>
            <li><p class="has-text-info">{{joke.double.first}}</p></li>
            <li><p class="has-text-info">{{joke.double.second}}</p></li>
        </ul>
    </div>
    <ring-loader v-else ring-loader class="column most-copied-snippets-ring is-narrow"></ring-loader>
</template>

<script>
    export default {
        props: ['joke'],
        data: () => {
            return {
                Auth: Auth
            }
        }
    }
</script>
