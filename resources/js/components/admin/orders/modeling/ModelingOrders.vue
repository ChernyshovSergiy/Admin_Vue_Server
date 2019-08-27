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
                            <v-btn
                                color="success"
                                dark
                                class="mb-2"
                                @click="add"
                                v-on="on"
                            >
                                {{ $t('add') }}
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline blue--text">{{
                                    formTitle
                                }}</span>
                            </v-card-title>
                            <v-form ref="form" v-model="valid" lazy-validation>
                                <v-card-text>
                                    <v-container grid-list-md>
                                        <v-layout wrap>
                                            <v-flex xs12 md6>
                                                <v-select
                                                    v-model="items.status_id"
                                                    :items="statuses"
                                                    prepend-inner-icon="new_releases"
                                                    :label="`${$t('status')}`"
                                                    return-object
                                                    item-text="title"
                                                    item-value="id"
                                                    :hint="
                                                        `${$t('order_status')}`
                                                    "
                                                    persistent-hint
                                                    :rules="statusRules"
                                                />
                                            </v-flex>
                                            <v-flex xs12 md6>
                                                <v-select
                                                    v-model="items.language_id"
                                                    :items="languages"
                                                    prepend-inner-icon="g_translate"
                                                    :label="
                                                        `${$t('lang_name')}`
                                                    "
                                                    return-object
                                                    item-text="name"
                                                    item-value="id"
                                                    :hint="
                                                        `${$t(
                                                            'customer_communication_language'
                                                        )}`
                                                    "
                                                    persistent-hint
                                                    :rules="languageRules"
                                                />
                                            </v-flex>
                                            <v-flex xs12>
                                                <v-text-field
                                                    v-model="items.link"
                                                    :label="`${$t('link')}`"
                                                    :hint="
                                                        `${$t(
                                                            'link_to_folder'
                                                        )}`
                                                    "
                                                    persistent-hint
                                                    :rules="linkRules"
                                                    :counter="500"
                                                />
                                            </v-flex>
                                            <v-flex xs12 md6>
                                                <v-text-field
                                                    v-model="items.name"
                                                    :label="
                                                        `${$t('customer_name')}`
                                                    "
                                                    :rules="nameRules"
                                                    type="text"
                                                    :counter="20"
                                                    required
                                                />
                                            </v-flex>
                                            <v-flex xs12 md6>
                                                <v-text-field
                                                    v-model="items.email"
                                                    :label="
                                                        `${$t(
                                                            'customer_email'
                                                        )}`
                                                    "
                                                    :rules="emailRules"
                                                    required
                                                />
                                            </v-flex>
                                            <v-flex xs12 md5>
                                                <v-checkbox
                                                    v-model="items.texturing"
                                                    :label="
                                                        `${$t('texturing')}`
                                                    "
                                                    value="1"
                                                    color="primary"
                                                />
                                            </v-flex>
                                            <v-flex xs12 md7>
                                                <v-select
                                                    v-model="items.executor_id"
                                                    :items="executors"
                                                    prepend-inner-icon="group"
                                                    :label="`${$t('executor')}`"
                                                    return-object
                                                    item-text="name"
                                                    item-value="id"
                                                />
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer />
                                    <v-btn
                                        color="primary"
                                        text
                                        outlined
                                        @click="close"
                                    >
                                        {{ $t('cancel') }}
                                    </v-btn>
                                    <v-btn
                                        :disabled="!valid"
                                        color="primary"
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
                    <v-btn text icon :href="'mailto:' + item.email">
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
            statuses: [],
            executors: [],
            items: [],
            valid: true,
            editedIndex: -1,
            search: '',
            dialog: false,
            statusRules: [v => !!v || this.$t('status_is_required')],
            languageRules: [v => !!v || this.$t('language_is_required')],
            nameRules: [
                v => !!v || this.$t('name_is_required'),
                v =>
                    (v || '').length >= 3 ||
                    this.$t('name_must_be_greater_than_3_characters'),
                v =>
                    (v || '').length <= 20 ||
                    this.$t('name_must_be_less_than_20_characters')
            ],
            emailRules: [
                v => !!v || this.$t('email_is_required'),
                v =>
                    /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/.test(
                        v
                    ) || this.$t('email_must_be_valid')
            ],
            linkRules: [
                v => !!v || this.$t('link_is_required'),
                v =>
                    /^(ftp|http|https):\/\/[^ "]+$/.test(v) ||
                    this.$t('link_must_be_valid'),
                v =>
                    (v || '').length <= 500 ||
                    this.$t('link_must_be_less_than_500_characters')
            ]
        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1
                ? this.$t('new_modeling_order')
                : this.$t('edit_modeling_order');
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
        // this.getLanguages();
        // this.getExecutors();
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
        add() {
            this.getStatuses();
            this.getExecutors();
            this.getLanguages();
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
                let self = this;
                await axios
                    .get(api.path('language'))
                    .then(req => {
                        req.data.data.forEach(function(lang) {
                            if (lang.is_active === 1) {
                                self.languages.push({
                                    id: lang.id,
                                    name: lang.name
                                });
                            }
                        });
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
        async getStatuses() {
            try {
                await axios
                    .get(api.path('statuses') + '/' + this.cLang)
                    .then(req => {
                        this.statuses = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchStatusError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeStatusError: ', e);
                return [];
            }
        },
        async getExecutors() {
            try {
                await axios
                    .get(api.path('executors') + '/' + 'modeling')
                    .then(req => {
                        this.executors = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchStatusError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeStatusError: ', e);
                return [];
            }
        },
        async getExecutor(id) {
            let self = this;
            try {
                await axios
                    .get(api.path('executor') + '/' + id)
                    .then(req => {
                        self.executors.push({
                            id: req.data.data.id,
                            name: req.data.data.name
                        });
                    })
                    .catch(e => {
                        console.log('fetchExecutorError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeExecutorError: ', e);
                return [];
            }
        },
        async getLang(id) {
            let self = this;
            try {
                await axios
                    .get(api.path('language') + '/' + id)
                    .then(req => {
                        self.languages.push({
                            id: req.data.data.id,
                            name: req.data.data.name
                        });
                    })
                    .catch(e => {
                        console.log('fetchLanguageError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeLanguageError: ', e);
                return [];
            }
        },
        close() {
            this.dialog = false;
            this.executors = [];
            this.languages = [];
            this.$refs.form.reset();
            this.editedIndex = -1;
        },
        async save() {
            if (this.$refs.form.validate()) {
                let payload = Object.assign({}, this.items);
                if (typeof payload.status_id === 'object') {
                    payload.status_id = payload.status_id.id;
                } else {
                    payload.status_id = this.items.status_id;
                }
                if (typeof payload.language_id === 'object') {
                    payload.language_id = payload.language_id.id;
                } else {
                    payload.language_id = this.items.language_id;
                }
                if (typeof payload.executor_id === 'object') {
                    payload.executor_id = payload.executor_id.id;
                } else {
                    payload.executor_id = this.items.executor_id;
                }
                payload.texturing = payload.texturing === '1' ? 1 : 0;
                if (this.editedIndex > -1) {
                    console.log(this.editedIndex);
                    try {
                        await axios
                            .put(
                                api.path('modeling') + '/' + this.editedIndex,
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
                            .post(api.path('modeling'), payload)
                            .then(() => {
                                this.initialize();
                                this.$refs.form.reset();
                                this.$toast.success(
                                    this.$t('order_add_successfully'),
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
            this.getStatuses();
            this.getExecutors();
            this.getLanguages();
            try {
                await axios
                    .get(api.path('modeling') + '/' + item)
                    .then(res => {
                        this.items = Object.assign({}, res.data.data);
                        this.items.texturing =
                            this.items.texturing === 1 ? '1' : null;
                        let self = this;
                        let user_id = this.items.executor_id;
                        let user = self.executors.some(function(executor) {
                            return executor.id === user_id;
                        });
                        if (!user && user_id !== null) {
                            this.getExecutor(user_id);
                        }
                        let lang_id = this.items.language_id;
                        let lang = self.languages.some(function(language) {
                            return language.id === lang_id;
                        });
                        if (!lang && lang_id!== null) {
                            this.getLang(lang_id);
                        }
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
