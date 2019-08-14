<template>
    <v-card>
        <v-data-table
            :headers="getHeaders()"
            :items="languages"
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
                        {{ $t('languages') }}
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

                            <v-card-text>
                                <v-container grid-list-md>
                                    <v-layout wrap>
                                        <v-flex xs12 sm6 md4>
                                            <v-select
                                                v-model="editedItem.is_active"
                                                :items="items"
                                                prepend-inner-icon="thumb_up"
                                                :label="`${$t('is_active')}`"
                                                return-object
                                                item-text="value"
                                                item-value="id"
                                            />
                                        </v-flex>
                                        <v-flex xs12 sm6 md4>
                                            <v-text-field
                                                v-model="
                                                    editedItem.flag_country
                                                "
                                                :label="`${$t('flag_country')}`"
                                                return-masked-value
                                                mask="aa"
                                            />
                                        </v-flex>
                                        <v-flex xs12 sm6 md4>
                                            <v-text-field
                                                v-model="editedItem.code"
                                                :label="`${$t('code')}`"
                                                return-masked-value
                                                mask="aa"
                                            />
                                        </v-flex>
                                        <v-flex xs12 sm6 md4>
                                            <v-text-field
                                                v-model="editedItem.iso"
                                                :label="`${$t('iso')}`"
                                                return-masked-value
                                                mask="aa-AA"
                                            />
                                        </v-flex>
                                        <v-flex xs12 sm6 md4>
                                            <v-text-field
                                                v-model="editedItem.file"
                                                :label="`${$t('file')}`"
                                                return-masked-value
                                                mask="aa-AA.js"
                                            />
                                        </v-flex>
                                        <v-flex xs12 sm6 md4>
                                            <v-text-field
                                                v-model="editedItem.name"
                                                :label="`${$t('name_lang')}`"
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
                                    @click="close"
                                >
                                    {{ $t('cancel') }}
                                </v-btn>
                                <v-btn color="blue darken-1" text @click="save">
                                    {{ $t('save') }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.action="{ item }">
                <!--                <v-icon small class="mr-2 blue&#45;&#45;text" @click="showItem(item)">-->
                <!--                    visibility-->
                <!--                </v-icon>-->
                <v-icon small class="orange--text" @click="editItem(item)">
                    edit
                </v-icon>
                <v-icon small class="red--text" @click="deleteItem(item.id)">
                    delete
                </v-icon>
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
            <template v-slot:item.flag="{ item }">
                <flag
                    style="font-size:25px"
                    :iso="item.flag_country"
                    :squared="false"
                />
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">
                    {{ $t('reset') }}
                </v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
// import { mapGetters } from 'vuex';
import axios from 'axios';
import { api } from '~/config';
export default {
    name: 'Languages',
    data() {
        return {
            dialog: false,
            trigger: false,
            search: '',
            activeIcon: '',
            selectItems: {
                id: 0,
                value: this.$t('disable')
            },
            languages: [],
            editedIndex: -1,
            oldEditItems: {
                is_active: 0,
                flag_country: '',
                code: '',
                iso: '',
                file: '',
                name: ''
            },
            editedItem: {
                is_active: 0,
                flag_country: '',
                code: '',
                iso: '',
                file: '',
                name: ''
            },
            defaultItem: {
                is_active: 0,
                flag_country: '',
                code: '',
                iso: '',
                file: '',
                name: ''
            }
        };
    },

    computed: {
        formTitle() {
            return this.editedIndex === -1
                ? this.$t('new_language')
                : this.$t('edit_language');
        },
        translate(val) {
            const trans = this.$t(val);
            console.log(trans);
            return trans;
        },
        items() {
            return [
                { id: 0, value: this.$t('disable') },
                { id: 1, value: this.$t('active') }
            ];
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },
    created() {
        this.initialize();
        // setInterval(()=> this.initialize(), 3000);
    },

    methods: {
        getHeaders() {
            return [
                {
                    text: this.$t('id'),
                    align: 'left',
                    value: 'id'
                },
                { text: this.$t('is_active'), value: 'is_active' },
                { text: this.$t('flag_country'), value: 'flag_country' },
                { text: this.$t('code'), value: 'code' },
                { text: this.$t('iso'), value: 'iso' },
                { text: this.$t('file'), value: 'file' },
                { text: this.$t('name_lang'), value: 'name' },
                { text: this.$t('flag'), value: 'flag', sortable: false },
                { text: this.$t('actions'), value: 'action', sortable: false }
            ];
        },
        async initialize() {
            try {
                await axios
                    .get(api.path('language'))
                    .then(req => {
                        this.languages = req.data.data;
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

        async triggerActiveItem(item) {
            this.trigger = true;
            this.editedIndex = this.languages.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.editedItem.is_active = this.editedItem.is_active === 1 ? 0 : 1;
            const payload = this.editedItem;
            const cLang = this.$i18n.locale;
            try {
                await axios
                    .put(api.path('language') + '/' + payload.id, {
                        id: payload.id,
                        is_active: payload.is_active,
                        flag_country: payload.flag_country,
                        code: payload.code,
                        iso: payload.iso,
                        file: payload.file,
                        name: payload.name,
                        cLang: cLang
                    })
                    .then(() => {
                        this.save();
                        this.$toast.success(
                            this.$t('language_successfully_updated'),
                            {
                                timeout: 10000,
                                icon: 'done_outline',
                                dismissable: false,
                                showClose: true
                            }
                        );
                    })
                    .catch(e => {
                        console.log('fetchError: ', e);
                        this.close();
                    });
            } catch (e) {
                console.log('languageEdit: ', e);
                this.close();
            }
        },

        editItem(item) {
            this.editedIndex = this.languages.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.oldEditItems = Object.assign({}, item);
            this.dialog = true;
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
                            .delete(api.path('language') + '/' + item)
                            .then(() => {
                                this.initialize();

                                this.$swal.fire(
                                    this.$t('deleted'),
                                    this.$t('has_been_deleted'),
                                    'success'
                                );
                            })
                            .catch(e => {
                                this.$toast.error(
                                    e.response.data.errors.code[0],
                                    {
                                        timeout: 10000,
                                        icon: 'error_outline',
                                        dismissable: false,
                                        showClose: true
                                    }
                                );
                            });
                    }
                })
                .catch(e => {
                    console.log(e);
                });
        },

        close() {
            this.dialog = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            }, 300);
        },

        async save() {
            if (this.editedIndex > -1 && this.trigger === false) {
                const payload = Object.assign({}, this.editedItem);
                if (typeof payload.is_active === 'object') {
                    payload.is_active = payload.is_active.id;
                }
                const cLang = this.$i18n.locale;
                try {
                    await axios
                        .put(api.path('language') + '/' + payload.id, {
                            id: payload.id,
                            is_active: payload.is_active,
                            flag_country: payload.flag_country,
                            code: payload.code,
                            iso: payload.iso,
                            file: payload.file,
                            name: payload.name,
                            cLang: cLang
                        })
                        .then(() => {
                            Object.assign(
                                this.languages[this.editedIndex],
                                this.editedItem
                            );
                            this.$toast.success(
                                this.$t('language_successfully_updated'),
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
                            this.editedItem = Object.assign(
                                {},
                                this.oldEditItems
                            );
                            this.$toast.error(e.response.data.errors.code[0], {
                                timeout: 10000,
                                icon: 'error_outline',
                                dismissable: false,
                                showClose: true
                            });
                        });
                } catch (e) {
                    console.log('languageEdit: ', e);
                }
            } else if (this.editedIndex > -1 && this.trigger === true) {
                Object.assign(
                    this.languages[this.editedIndex],
                    this.editedItem
                );
                this.trigger = false;
                this.close();
            } else {
                const payload = Object.assign({}, this.editedItem);
                if (typeof payload.is_active === 'object') {
                    payload.is_active = payload.is_active.id;
                }
                const cLang = this.$i18n.locale;
                try {
                    await axios
                        .post(api.path('language'), {
                            is_active: payload.is_active,
                            flag_country: payload.flag_country,
                            code: payload.code,
                            iso: payload.iso,
                            file: payload.file,
                            name: payload.name,
                            cLang: cLang
                        })
                        .then(() => {
                            this.initialize();
                            this.$toast.success(
                                this.$t('language_add_successfully'),
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
                            this.$toast.error(e.response.data.errors.code[0], {
                                timeout: 10000,
                                icon: 'error_outline',
                                dismissable: false,
                                showClose: true
                            });
                        });
                } catch (e) {
                    console.log('languageAdd ', e);
                }
            }
        },
        activateIcon(val) {
            return val === 1 ? 'thumb_up' : 'lock';
        }
    }
};
</script>

<style scoped></style>
