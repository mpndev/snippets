<template>
    <div :class="{ 'darkmod': Auth.isDarkMod() }" class="background-is-grey-lighter" style="min-height: 100vh;">
        <cookie-notification></cookie-notification>
        <message></message>

        <navbar></navbar>

        <div class="section has-margin-top-45">
            <div>
                <router-view :key="$route.fullPath"></router-view>
            </div>
        </div>

        <div class="section">
            <faq />
        </div>
    </div>
</template>

<script>
    import Navbar from "./Navbar";
    import CookieNotification from "./CookieNotification";

    export default {
        data: () => {
            return {
                Auth: Auth
            }
        },
        components: {
            navbar: Navbar,
            CookieNotification: CookieNotification
        },
    }
</script>
