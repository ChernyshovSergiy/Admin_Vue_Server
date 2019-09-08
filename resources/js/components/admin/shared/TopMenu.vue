<template>
    <v-app-bar
        dark
        :clipped-left="$vuetify.breakpoint.mdAndUp"
        fixed
        app
        color="#052238"
    >
        <v-app-bar-nav-icon @click.stop="navToggle" />
        <v-toolbar-title class="yellow--text">
            {{ siteName }}
        </v-toolbar-title>
        <v-text-field
            flat
            solo-inverted
            hide-details
            prepend-inner-icon="search"
            :label="`${$t('search')}`"
            class="hidden-sm-and-down ml-5"
        />
        <v-spacer />
        <div class="text-center">
            <v-menu open-on-hover offset-y color="#052238">
                <template v-slot:activator="{ on }">
                    <v-btn
                        v-model="filteredLanguage"
                        class="pa-0"
                        depressed
                        large
                        text
                        v-on="on"
                    >
                        <v-flex
                            id="flag"
                            align-center
                            justify-center
                            layout
                            style="font-size:30px"
                        >
                            <flag
                                :iso="filteredLanguage.flagCountry"
                                :squared="false"
                                :title="filteredLanguage.title"
                            />
                            <v-icon dark right>
                                expand_more
                            </v-icon>
                        </v-flex>
                    </v-btn>
                </template>
                <v-list dark color="#052238" class="pa-0">
                    <div
                        v-for="loc in languages"
                        :key="loc.code"
                        @click="setLocale(loc.language)"
                    >
                        <v-divider />
                        <div
                            v-if="
                                loc.flagCountry !== filteredLanguage.flagCountry
                            "
                        >
                            <v-list-item @click="light = !light">
                                <v-list-item-action style="font-size:24px">
                                    <flag
                                        :iso="loc.flagCountry"
                                        :squared="false"
                                        :title="loc.title"
                                    />
                                </v-list-item-action>

                                <v-list-item-content>
                                    <v-list-item-title v-text="loc.title" />
                                </v-list-item-content>
                            </v-list-item>
                        </div>
                    </div>
                </v-list>
            </v-menu>
        </div>
    </v-app-bar>
</template>

<script>
import { settings } from '~/config';

export default {
    data: () => ({
        siteName: settings.siteName,
        light: false,
        languages: [
            { flagCountry: 'gb', language: 'en', title: 'English' },
            { flagCountry: 'ru', language: 'ru', title: 'Русский' },
            { flagCountry: 'es', language: 'es', title: 'Español' },
            { flagCountry: 'ua', language: 'ua', title: 'Український' },
            { flagCountry: 'de', language: 'de', title: 'Deutsch' },
            // { flagCountry: 'it', language: 'it', title: 'Italiano' },
            // { flagCountry: 'fr', language: 'fr', title: 'Le français' },
            // { flagCountry: 'sa', language: 'sa', title: 'العربية' },
            // { flagCountry: 'cn', language: 'cn', title: '中国' },
            // { flagCountry: 'jp', language: 'jp', title: '日本語' }
        ]
    }),
    computed: {
        filteredLanguage() {
            const self = this;
            return this.languages
                .filter(function(language) {
                    return language.language === self.$i18n.locale;
                })
                .pop();
        }
    },

    methods: {
        navToggle() {
            this.$emit('nav-toggle');
        },
        setLocale(locale) {
            this.$emit('set-locale', locale);
        }
    }
};
</script>
