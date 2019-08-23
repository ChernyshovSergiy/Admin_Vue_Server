<template>
    <v-card>
        <v-data-table
            :headers="getHeaders()"
            :items="modeling_orders"
            :search="search"
            sort-by="avery_column"
            class="elevation-3"
            :footer-props="{
                showFirstLastPage: true,
                firstIcon: 'mdi-arrow-collapse-left',
                lastIcon: 'mdi-arrow-collapse-right',
                prevIcon: 'mdi-minus',
                nextIcon: 'mdi-plus',
                'items-per-page': 'Строк на странице'
            }"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title class="primary--text headline">
                        {{ $t('modeling_page') }}
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
                            <v-form ref="form" v-model="valid" lazy-validation>
                                <v-card-text>
                                    <v-container grid-list-md>
                                        <v-layout
                                            v-for="language in languages"
                                            :key="language"
                                            wrap
                                        >
                                            <v-flex xs12>
                                                <v-text-field
                                                    v-model="items[language]"
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
            <template v-slot:item.materials="{ item }">
                <v-btn
                    text
                    target="_blank"
                    icon
                    class="blue--text"
                    :href="item.link"
                >
                    <v-icon small>
                        visibility
                    </v-icon>
                </v-btn>
            </template>
            <template v-slot:item.texture="{ item }">
                <v-icon :class="item.texturing === 1 ? 'green--text' : ''">
                    {{ item.texturing === 1 ? 'done' : '' }}
                </v-icon>
            </template>
            <template v-slot:item.mailto="{ item }">
                <div class="caption">
                    <v-btn
                        text
                        icon
                        :href="'mailto:' + item.email"
                        target="_blank"
                    >
                        <v-icon small color="teal darken-2">
                            email
                        </v-icon>
                    </v-btn>
                    {{ item.email }}
                </div>
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
    name: 'ModelingOrders',
    data() {
        return {
            modeling_orders: [],
            languages: [],
            items: [],
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
                    (v || '').length <= 20 ||
                    this.$t('title_must_be_less_than_20_characters')
            ]
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
                { text: this.$t('status'), value: 'status' },
                { text: this.$t('lang_name'), value: 'language' },
                { text: this.$t('materials'), value: 'materials' },
                { text: this.$t('name'), value: 'name' },
                { text: this.$t('email'), value: 'mailto' },
                {
                    text: this.$t('texturing'),
                    align: 'center',
                    value: 'texture'
                },
                { text: this.$t('executor'), value: 'executor' },
                { text: this.$t('actions'), value: 'action', sortable: false }
            ];
        },
        async initialize() {
            try {
                await axios
                    .get(api.path('modelings') + '/' + this.cLang)
                    .then(req => {
                        this.modeling_orders = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchOrdersError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeOrdersError: ', e);
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
        close() {
            this.dialog = false;
            this.$refs.form.reset();
            this.editedIndex = -1;
        },
        async save() {
            if (this.$refs.form.validate()) {
                let self = this;
                let payload = {};
                this.languages.forEach(function(lang) {
                    payload[lang] = self.items[lang];
                });
                if (this.editedIndex > -1) {
                    try {
                        await axios
                            .put(
                                api.path('status') + '/' + this.editedIndex,
                                payload
                            )
                            .then(() => {
                                this.initialize();
                                this.$refs.form.reset();
                                this.$toast.success(
                                    this.$t('status_successfully_updated'),
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
                        console.log('ErrorStatusEditSave: ', e);
                    }
                } else {
                    try {
                        await axios
                            .post(api.path('status'), payload)
                            .then(() => {
                                this.initialize();
                                this.$refs.form.reset();
                                this.$toast.success(
                                    this.$t('status_add_successfully'),
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
                        console.log('ErrorStatusAdd: ', e);
                    }
                }
            }
        },
        async editItem(item) {
            try {
                await axios
                    .get(api.path('status') + '/' + item)
                    .then(res => {
                        let self = this;
                        let status = {};
                        this.languages.forEach(function(lang) {
                            status[lang] = res.data.data.title[lang];
                        });
                        self.items = Object.assign({}, status);
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
                console.log('ErrorStatusEdit: ', e);
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
                            .delete(api.path('status') + '/' + item)
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
        }
    }
};
</script>

<style scoped></style>
