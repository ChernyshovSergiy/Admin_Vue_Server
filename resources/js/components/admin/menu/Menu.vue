<template>
    <v-card>
        <v-data-table
            :headers="getHeaders()"
            :items="menus"
            :search="search"
            sort-by="avery_column"
            class="elevation-3"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title class="primary--text headline">
                        {{ $t('menu') }}
                    </v-toolbar-title>
                    <v-divider class="mx-4" inset vertical />
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        :label="`${$t('search')}`"
                        single-line
                        hide-details
                        class="ml-12"
                        clearable
                    />
                    <v-spacer />
                    <v-dialog v-model="dialog" max-width="auto">
                        <template v-slot:activator="{ on }">
                            <v-btn color="success" dark class="mb-2" v-on="on">
                                {{ $t('add') }}
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-form ref="form" v-model="valid" lazy-validation>
                                <v-card-text>
                                    <v-container grid-list-md>
                                        <v-flex xs12>
                                            <v-select
                                                v-model="items.is_active"
                                                :items="active"
                                                prepend-inner-icon="thumb_up"
                                                :label="`${$t('is_active')}`"
                                                return-object
                                                item-text="value"
                                                item-value="id"
                                            />
                                        </v-flex>
                                        <v-flex xs12>
                                            <v-select
                                                v-model="items.section"
                                                :items="section"
                                                prepend-inner-icon="receipt"
                                                :label="`${$t('section')}`"
                                                return-object
                                                item-text="value"
                                                item-value="id"
                                            />
                                        </v-flex>
                                        <v-layout
                                            v-for="language in languages"
                                            :key="language"
                                            wrap
                                        >
                                            <v-flex xs12>
                                                <v-text-field
                                                    v-model="items.title[language]"
                                                    :label="
                                                        `${$t('title') +
                                                            ': ' +
                                                            language}`
                                                    "
                                                    :rules="titleRules"
                                                    type="text"
                                                    :counter="20"
                                                    required
                                                />
                                            </v-flex>
                                        </v-layout>
                                        <v-flex xs12>
                                            <v-text-field
                                                v-model="items.url"
                                                :label="`${$t('url')}`"
                                                :rules="titleRules"
                                                type="text"
                                                :counter="20"
                                                required
                                            />
                                        </v-flex>
                                        <v-flex xs12>
                                            <v-select
                                                v-model="items.parent"
                                                :items="parent"
                                                prepend-inner-icon="publish"
                                                :label="`${$t('parent')}`"
                                                return-object
                                                item-text="value"
                                                item-value="id"
                                            />
                                        </v-flex>
                                        <v-flex xs12>
                                            <v-text-field
                                                v-model="items.sort"
                                                :label="`${$t('sort')}`"
                                                :rules="sortRules"
                                                type="text"
                                                required
                                            />
                                        </v-flex>
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
                                        :disabled="!valid"
                                        color="blue darken-1"
                                        outlined
                                        @click="save"
                                    >
                                        {{ $t('save') }}
                                    </v-btn>
                                </v-card-actions>
                            </v-form>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.is_active="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    :class="item.is_active === 1 ? 'green--text' : 'grey--text'"
                    @click="triggerActiveItem(item)"
                >
                    {{ activateIcon(item.is_active) }}
                </v-icon>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon small class="orange--text" @click="editItem(item.id)">
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
    name: 'Menu',
    data() {
        return {
            menus: [],
            languages: [],
            parent: [],
            triggers: {
                is_active: 0,
                section: 0,
                title: { en: '' },
                url: '',
                parent: 0,
                sort: 0
            },
            items: {
                is_active: 0,
                section: 0,
                title: { en: '' },
                url: '',
                parent: 0,
                sort: 0
            },
            blocks: ['our_company', 'help', 'partner', '3dmriya'],
            valid: true,
            editedIndex: -1,
            activeLanguages: [],
            search: '',
            dialog: false,
            titleRules: [
                v => !!v || this.$t('title_is_required'),
                v =>
                    (v || '').length >= 3 ||
                    this.$t('title_must_be_greater_than_3_characters'),
                v =>
                    (v || '').length <= 30 ||
                    this.$t('title_must_be_less_than_30_characters')
            ],
            sortRules: [
                v => !!v || this.$t('sort_is_required'),
                v => /^\d+$/.test(v) || this.$t('sort_must_be_numeric')
            ]
        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1
                ? this.$t('add_menu_point')
                : this.$t('edit_menu_point');
        },
        cLang() {
            return this.$i18n.locale;
        },
        active() {
            return [
                { id: 0, value: this.$t('disable') },
                { id: 1, value: this.$t('active') }
            ];
        },
        section() {
            return [
                { id: 0, value: this.$t('our_company') },
                { id: 1, value: this.$t('help') },
                { id: 2, value: this.$t('partner') },
                { id: 3, value: this.$t('3dmriya') }
            ];
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
                { text: this.$t('is_active'), value: 'is_active' },
                { text: this.$t('section'), value: 'section' },
                { text: this.$t('title'), value: 'title' },
                { text: this.$t('url'), value: 'url' },
                { text: this.$t('parent'), value: 'parent' },
                { text: this.$t('sort'), value: 'sort' },
                { text: this.$t('actions'), value: 'action', sortable: false }
            ];
        },
        async initialize() {
            try {
                await axios
                    .get(api.path('menus') + '/' + this.cLang)
                    .then(req => {
                        this.menus = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchMenusError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeMenusError: ', e);
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
                        console.log('fetchLangError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeLangError: ', e);
                return [];
            }
        },
        async triggerActiveItem(item) {
            this.triggers = Object.assign({}, item);
            this.triggers.is_active = this.triggers.is_active === 1 ? 0 : 1;
            const active = {
                id: this.triggers.id,
                is_active: this.triggers.is_active
            };
            const payload = Object.assign({}, active);
            try {
                await axios
                    .put(api.path('menu') + '/' + this.triggers.id, payload)
                    .then(() => {
                        this.initialize();
                        this.triggers = [];
                        this.$toast.success(
                            this.$t('menu_successfully_updated'),
                            {
                                timeout: 10000,
                                icon: 'done_outline',
                                dismissable: false,
                                showClose: true
                            }
                        );
                    })
                    .catch(e => {
                        console.log('triggerError: ', e);
                    });
            } catch (e) {
                console.log('menuTriggerEditError: ', e);
            }
        },
        close() {
            this.dialog = false;
            this.$refs.form.reset();
            this.editedIndex = -1;
        },
        async save() {
            if (this.$refs.form.validate()) {
                let self = this;
                let payload = Object.assign({}, this.items);
                delete payload.headline;
                delete payload.sub_headline;
                delete payload.text;
                if (typeof payload.is_active === 'object') {
                    payload.is_active = payload.is_active.id;
                } else {
                    payload.is_active = this.items.is_active;
                }
                this.languages.forEach(function(lang) {
                    payload['headline:' + lang] = self.items.headline[lang];
                    payload['sub_headline:' + lang] =
                        self.items.sub_headline[lang];
                    payload['text:' + lang] = self.items.text[lang];
                });

                if (this.editedIndex > -1) {
                    try {
                        await axios
                            .put(
                                api.path('content') + '/' + this.editedIndex,
                                payload
                            )
                            .then(() => {
                                this.initialize();
                                this.$refs.form.reset();
                                this.$toast.success(
                                    this.$t('content_successfully_updated'),
                                    {
                                        timeout: 10000,
                                        icon: 'done_outline',
                                        dismissable: false,
                                        showClose: true
                                    }
                                );
                                this.close();
                            })
                            .catch(e => {
                                this.$toast.error(
                                    e.response.data.errors.en[0],
                                    {
                                        timeout: 10000,
                                        icon: 'error_outline',
                                        dismissable: false,
                                        showClose: true
                                    }
                                );
                            });
                    } catch (e) {
                        console.log('ErrorContentEditSave: ', e);
                    }
                } else {
                    try {
                        await axios
                            .post(api.path('content'), payload)
                            .then(() => {
                                this.initialize();
                                this.$refs.form.reset();
                                this.$toast.success(
                                    this.$t('content_add_successfully'),
                                    {
                                        timeout: 10000,
                                        icon: 'done_outline',
                                        dismissable: false,
                                        showClose: true
                                    }
                                );
                                this.close();
                            })
                            .catch(e => {
                                this.$toast.error(
                                    e.response.data.errors.en[0],
                                    {
                                        timeout: 10000,
                                        icon: 'error_outline',
                                        dismissable: false,
                                        showClose: true
                                    }
                                );
                            });
                    } catch (e) {
                        console.log('ErrorContentAdd: ', e);
                    }
                }
            }
        },
        async editItem(item) {
            try {
                await axios
                    .get(api.path('menu') + '/' + item)
                    .then(res => {
                        this.items = Object.assign({}, res.data.data);
                        this.editedIndex = item;
                        this.dialog = true;
                    })
                    .catch(e => {
                        this.$toast.error(e.response.data.errors, {
                            timeout: 10000,
                            icon: 'error_outline',
                            dismissable: false,
                            showClose: true
                        });
                    });
            } catch (e) {
                console.log('ErrorContentEdit: ', e);
            }
        },
        deleteItem(item) {
            this.$swal
                .fire({
                    title: this.$t('are_you_sure'),
                    text: this.$t('you_wont_revert'),
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#25b055',
                    cancelButtonColor: '#d33',
                    confirmButtonText: this.$t('yes_delete_it'),
                    cancelButtonText: this.$t('cancel')
                })
                .then(result => {
                    if (result.value) {
                        axios
                            .delete(api.path('content') + '/' + item)
                            .then(() => {
                                this.initialize();

                                this.$swal.fire(
                                    this.$t('deleted'),
                                    this.$t('has_been_deleted'),
                                    'success'
                                );
                            })
                            .catch(e => {
                                this.$toast.error(e.response.data.errors, {
                                    timeout: 10000,
                                    icon: 'error_outline',
                                    dismissable: false,
                                    showClose: true
                                });
                            });
                    }
                })
                .catch(e => {
                    console.log('DeleteErrors: ', e);
                });
        },
        activateIcon(val) {
            return val === 1 ? 'thumb_up' : 'lock';
        }
    }
};
</script>

<style scoped></style>
