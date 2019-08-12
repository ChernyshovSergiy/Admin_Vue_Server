<template>
    <div class="fill-height">
        <top-menu @nav-toggle="navToggle" @set-locale="setLocale"></top-menu>

        <v-content class="mt-10">
            <v-container
                fluid
                fill-height
                justify-center
                align-center
                class="mt-10"
            >
                <transition name="fade" mode="out-in">
                    <router-view></router-view>
                </transition>
            </v-container>
        </v-content>
    </div>
</template>

<script>
import TopMenu from '~/components/admin/shared/TopMenu';
export default {
    components: {
        TopMenu
    },
    methods: {
        navToggle() {
            this.mini = !this.mini;
        },
        setLocale(locale) {
            import(`~/lang/${locale}.json`).then(msg => {
                this.$i18n.setLocaleMessage(locale, msg);
                this.$i18n.locale = locale;
            });
        }
    }
};
</script>
