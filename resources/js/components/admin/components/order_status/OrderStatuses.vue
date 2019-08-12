<template>
    <v-card>
        <v-data-table
            :headers="getHeaders()"
            :items="statuses"
            :search="search"
            sort-by="avery_column"
            class="elevation-3"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title class="primary--text headline">
                        {{ $t('order_status') }}
                    </v-toolbar-title>
                    <v-divider class="mx-4" inset vertical />
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        :label="`${$t('search')}`"
                        single-line
                        hide-details
                        class="ml-12"
                    />
                    <v-spacer />
                    <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="success" dark class="mb-2" v-on="on">
                                {{ $t('add') }}
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container grid-list-md>
                                    <v-layout v-for="language in languages" :key="language" wrap>
                                        <v-flex xs12>
                                            <v-text-field
                                                v-model="editedItem.language"
                                                :label="`${$t('title') + ': ' + language}`"
                                            />
                                        </v-flex>
                                    </v-layout>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer />
                                <v-btn
                                    color="blue darken-1"
                                    text
                                    outlined
                                    @click="close"
                                >
                                    {{ $t('cancel') }}
                                </v-btn>
                                <v-btn
                                    color="blue darken-1"
                                    text
                                    outlined
                                    @click="save"
                                >
                                    {{ $t('save') }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon small class="orange--text" @click="editItem(item)">
                    edit
                </v-icon>
                <v-icon small class="red--text" @click="deleteItem(item.id)">
                    delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="success" @click="initialize">
                    {{ $t('reset') }}
                </v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import axios from 'axios';
import { api } from '~/config';
export default {
    data() {
        return {
            statuses: [],
            languages: [],
            editedIndex: -1,
            activeLanguages: [],
            search: '',
            dialog: false,
            editedItem: {
                title: ''
            },
            defaultItem: {
                title: ''
            }
        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1
                ? this.$t('new_status')
                : this.$t('edit_status');
        },
        cLang() {
            return this.$i18n.locale;
        }
    },
    watch: {
        cLang() {
            this.initialize();
        }
    },
    created() {
        this.initialize();
        this.getLanguages();
    },

    methods: {
        getHeaders() {
            return [
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('title'), value: 'title' },
                { text: this.$t('actions'), value: 'action', sortable: false }
            ];
        },
        async initialize() {
            try {
                await axios
                    .get(api.path('status') + '/' + this.cLang)
                    .then(req => {
                        this.statuses = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeError: ', e);
                return [];
            }
        },
        async getLanguages() {
            try {
                await axios
                    .get(api.path('languages'))
                    .then(req => {
                        this.languages = req.data;
                    })
                    .catch(e => {
                        console.log('fetchError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeError: ', e);
                return [];
            }
        },
        close() {
            this.dialog = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            }, 300);
        },
        save() {
            if (this.editedIndex > -1) {
                Object.assign(this.statuses[this.editedIndex], this.editedItem);
            } else {
                this.statuses.push(this.editedItem);
            }
            this.close();
        }
    }
};
</script>

<style scoped></style>
