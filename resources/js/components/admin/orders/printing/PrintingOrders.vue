<template>
    <v-card>
        <v-data-table
            :headers="getHeaders()"
            :items="printing_orders"
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
                        {{ $t('printing_page') }}
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
                    <v-dialog v-model="dialog" max-width="550px">
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
                                                    prepend-inner-icon="link"
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
                                                    prepend-inner-icon="person"
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
                                                    prepend-inner-icon="email"
                                                    :label="
                                                        `${$t(
                                                            'customer_email'
                                                        )}`
                                                    "
                                                    :rules="emailRules"
                                                    required
                                                />
                                            </v-flex>
                                            <v-flex xs12 md4>
                                                <v-checkbox
                                                    v-model="items.hollow"
                                                    :label="
                                                        `${$t('hollow_info')}`
                                                    "
                                                    value="1"
                                                    color="green"
                                                />
                                            </v-flex>
                                            <v-flex xs12 md4>
                                                <v-checkbox
                                                    v-model="items.support"
                                                    :label="
                                                        `${$t('support_info')}`
                                                    "
                                                    value="1"
                                                    color="orange"
                                                />
                                            </v-flex>
                                            <v-flex xs12 md4>
                                                <v-checkbox
                                                    v-model="
                                                        items.post_processing
                                                    "
                                                    :label="
                                                        `${$t(
                                                            'post_processing_info'
                                                        )}`
                                                    "
                                                    value="1"
                                                    color="primary"
                                                />
                                            </v-flex>
                                            <v-flex v-show="!other" xs12>
                                                <v-select
                                                    v-model="items.size_id"
                                                    :items="sizes"
                                                    prepend-inner-icon="aspect_ratio"
                                                    :label="
                                                        `${$t(
                                                            'collection_size'
                                                        )}`
                                                    "
                                                    return-object
                                                    item-text="value"
                                                    item-value="id"
                                                />
                                            </v-flex>
                                            <v-layout v-show="other">
                                                <v-flex xs12 md6>
                                                    <v-select
                                                        v-model="items.size_id"
                                                        :items="sizes"
                                                        prepend-inner-icon="aspect_ratio"
                                                        :label="
                                                            `${$t(
                                                                'collection_size'
                                                            )}`
                                                        "
                                                        return-object
                                                        item-text="value"
                                                        item-value="id"
                                                    />
                                                </v-flex>
                                                <v-flex xs12 md6>
                                                    <v-text-field
                                                        v-model.trim="
                                                            items.height
                                                        "
                                                        prepend-inner-icon="unfold_less"
                                                        name="height"
                                                        :label="
                                                            `${$t('height')}`
                                                        "
                                                        type="height"
                                                        :rules="dynamicRules"
                                                        data-cy="printingHeightField"
                                                        required
                                                    />
                                                </v-flex>
                                            </v-layout>
                                            <v-flex xs12 md6>
                                                <v-select
                                                    v-model="items.material"
                                                    :items="materials"
                                                    prepend-inner-icon="texture"
                                                    :label="`${$t('material')}`"
                                                    return-object
                                                    item-text="value"
                                                    item-value="id"
                                                    :hint="`${$t('3d_printing_material')}`"
                                                    persistent-hint
                                                    :rules="selectMaterialRules"
                                                />
                                            </v-flex>
                                            <v-flex xs12 md6>
                                                <v-select
                                                    v-model="items.quality"
                                                    prepend-inner-icon="high_quality"
                                                    :items="quality"
                                                    item-text="value"
                                                    item-value="id"
                                                    :rules="selectQualityRules"
                                                    :label="`${$t('quality')}`"
                                                    required
                                                    return-object
                                                ></v-select>
                                            </v-flex>
                                            <v-flex xs12 md6>
                                                <v-text-field
                                                    v-model.trim="items.quantity"
                                                    v-mask="quantityMask"
                                                    prepend-inner-icon="shopping_basket"
                                                    name="quantity"
                                                    :label="`${$t('quantity')}`"
                                                    type="text"
                                                    :rules="quantityRules"
                                                    required
                                                />
                                            </v-flex>
                                            <v-flex xs12 md6>
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
                                            <v-layout row fill-height justify-center>
                                                <div class="title font-weight-light mt-3">
                                                    {{ $t('recipient_address_title') }}
                                                </div>
                                            </v-layout>
                                            <v-layout row wrap align-space-around fill-height >
                                                <v-flex xs12 sm6>
                                                    <v-autocomplete
                                                        v-model="items.country"
                                                        :hint="countryInfo"
                                                        prepend-inner-icon="language"
                                                        :items="countries"
                                                        :filter="customFilterCountry"
                                                        item-text="country_name"
                                                        item-value="country_alpha2_code"
                                                        :rules="selectCountryRules"
                                                        :label="`${$t('country')}`"
                                                        required
                                                        persistent-hint
                                                        return-object
                                                    ></v-autocomplete>
                                                </v-flex>
                                                <v-spacer />
                                                <v-flex xs12 sm6>
                                                    <v-text-field
                                                        v-model.trim="items.zip_code"
                                                        :disabled="checkCountrySelect"
                                                        prepend-inner-icon="flag"
                                                        name="zip"
                                                        :label="`${$t('zip')}`"
                                                        type="zip"
                                                        :rules="zipRules"
                                                        validate-on-blur
                                                        required
                                                        return-masked-value
                                                        :hint="hintZip"
                                                        persistent-hint
                                                        :mask="zipMask"
                                                    >
                                                    </v-text-field>
                                                </v-flex>
                                            </v-layout>
                                            <v-layout row wrap align-space-around fill-height >
                                                <v-flex xs12 sm6>
                                                    <v-text-field
                                                        v-model.trim="items.state"
                                                        :disabled="checkZipCodeSelect"
                                                        prepend-inner-icon="location_on"
                                                        name="state"
                                                        :label="`${$t('state')}`"
                                                        type="state"
                                                    >
                                                    </v-text-field>
                                                </v-flex>
                                                <v-spacer />
                                                <v-flex xs12 sm6>
                                                    <v-text-field
                                                        v-if="!zipGet"
                                                        v-model.trim="items.city"
                                                        :disabled="checkZipCodeSelect"
                                                        prepend-inner-icon="location_city"
                                                        name="city"
                                                        :rules="cityRules"
                                                        :label="`${$t('city')}`"
                                                        type="city"
                                                    >
                                                    </v-text-field>
                                                    <v-select
                                                        v-if="zipGet"
                                                        v-model.trim="selectCity"
                                                        :disabled="checkZipCodeSelect"
                                                        :hint="`${selectCity.place_name},
                                                         ${selectCity.state_abbreviation}`"
                                                        prepend-inner-icon="location_city"
                                                        :items="cities"
                                                        item-text="place_name"
                                                        :label="`${$t('city')}`"
                                                        required
                                                        persistent-hint
                                                        return-object
                                                    >
                                                    </v-select>
                                                </v-flex>
                                            </v-layout>
                                            <v-layout row wrap align-space-around fill-height>
                                                <v-flex xs12 sm6>
                                                    <v-text-field
                                                        v-model.trim="items.address"
                                                        :disabled="checkZipCodeSelect"
                                                        prepend-inner-icon="where_to_vote"
                                                        name="address"
                                                        :rules="addressRules"
                                                        :label="`${$t('address')}`"
                                                        type="address"
                                                    >
                                                    </v-text-field>
                                                </v-flex>
                                                <v-spacer />
                                                <v-flex xs12 sm6>
                                                    <v-text-field
                                                        v-model.trim="items.phone"
                                                        v-mask="maskPhone"
                                                        :disabled="checkZipCodeSelect"
                                                        prepend-icon="phone_iphone"
                                                        name="phone"
                                                        :rules="phoneRules"
                                                        :label="`${$t('phone')}`"
                                                        type="phone"
                                                    >
                                                    </v-text-field>
                                                </v-flex>
                                            </v-layout>
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
                <a target="_blank" class="link" :href="item.link">
                    <v-icon small color="primary">
                        visibility
                    </v-icon>
                </a>
            </template>
            <template v-slot:item.address="{ item }">
                <div class="caption">
                    <a
                        :href="'mailto:' + item.email"
                        target="_blank"
                        class="link"
                    >
                        <v-icon small color="teal darken-2">
                            email
                        </v-icon>
                    </a>
                    {{ item.email }}
                </div>
                <div class="caption">
                    <a
                        v-if="item.map_link !== '0'"
                        :href="item.map_link"
                        target="_blank"
                        class="link"
                    >
                        <v-icon small color="orange darken-2">
                            where_to_vote
                        </v-icon>
                    </a>
                    <v-icon v-else small color="gray darken-2">
                        where_to_vote
                    </v-icon>
                    {{ item.position }}
                </div>
                <div class="caption">
                    <v-icon small color="blue">
                        phone_iphone
                    </v-icon>
                    {{ item.phone }}
                </div>
            </template>
            <template v-slot:item.options="{ item }">
                <v-tooltip right>
                    <template v-slot:activator="{ on }">
                        <v-icon
                            :style="
                                Number(item.option[0]) === 1
                                    ? { cursor: 'pointer' }
                                    : ''
                            "
                            small
                            class="mr-2"
                            :class="
                                Number(item.option[0]) === 1
                                    ? 'green--text'
                                    : 'grey--text'
                            "
                            v-on="on"
                        >
                            donut_large
                        </v-icon>
                    </template>
                    <span v-show="Number(item.option[0]) === 1" class="body-1">
                        {{ $t('hollow_info') }}
                    </span>
                </v-tooltip>
                <v-tooltip right>
                    <template v-slot:activator="{ on }">
                        <v-icon
                            :style="
                                Number(item.option[1]) === 1
                                    ? { cursor: 'pointer' }
                                    : ''
                            "
                            small
                            class="mr-2"
                            :class="
                                Number(item.option[1]) === 1
                                    ? 'orange--text'
                                    : 'grey--text'
                            "
                            v-on="on"
                        >
                            nature
                        </v-icon>
                    </template>
                    <span v-show="Number(item.option[1]) === 1" class="body-1">
                        {{ $t('support_info') }}
                    </span>
                </v-tooltip>
                <v-tooltip right>
                    <template v-slot:activator="{ on }">
                        <v-icon
                            :style="
                                Number(item.option[2]) === 1
                                    ? { cursor: 'pointer' }
                                    : ''
                            "
                            small
                            class="mr-2"
                            :class="
                                Number(item.option[2]) === 1
                                    ? 'blue--text'
                                    : 'grey--text'
                            "
                            v-on="on"
                        >
                            verified_user
                        </v-icon>
                    </template>
                    <span v-show="Number(item.option[2]) === 1" class="body-1">
                        {{ $t('post_processing_info') }}
                    </span>
                </v-tooltip>
            </template>
            <template v-slot:item.sizes="{ item }">
                <v-icon
                    small
                    :class="
                        item.size.endsWith('mm') ? 'blue--text' : 'green--text'
                    "
                >
                    {{
                        item.size.endsWith('mm')
                            ? 'height'
                            : 'perm_data_setting'
                    }}
                </v-icon>
                {{ item.size }}
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
    name: 'PrintingOrders',
    data() {
        return {
            printing_orders: [],
            languages: [],
            statuses: [],
            executors: [],
            countries: [],
            items: [],
            sizes: [],
            valid: true,
            editedIndex: -1,
            search: '',
            dialog: false,

            zipGet: false,
            maskPhone: '(###) ###-##-##',
            quantityMask: '######',
            zipFlag: 'checkChange',
            zipFlagChange: 'checkZipCodeChange',
            zipMask: '',
            zipRange: '',
            zipCharacters: '8',
            cities: [],

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
            ],
            heightRules: [
                v => !!v || this.$t('height_is_required'),
                v => /^\d+$/.test(v) || this.$t('height_must_be_numeric'),
                v =>
                    Number(v) >= 25 ||
                    this.$t('height_must_be_more_25_millimeters'),
                v =>
                    Number(v) <= 185 ||
                    this.$t('height_must_be_less_185_millimeters')
            ],
            selectMaterialRules: [v => !!v || this.$t('material_is_required')],
            selectQualityRules: [v => !!v || this.$t('quality_is_required')],
            selectCountryRules: [
                v => v.country_name !== '' || this.$t('country_is_required')
            ],
            zipRules: [v => !!v || this.$t('zip_is_required')],
            quantityRules: [
                v => !!v || this.$t('quantity_is_required'),
                v => /^\d+$/.test(v) || this.$t('quantity_must_be_numeric'),
                v =>
                    v <= 999999 ||
                    this.$t('quantity_must_be_less_than_6_numbers')
            ],
            cityRules: [
                v => !!v || this.$t('city_is_required'),
                v =>
                    /^[a-zA-Z0-9А-Яа-я_]+( [a-zA-Z0-9А-Яа-я_]+)*$/.test(v) ||
                    this.$t('city_must_be_word'),
                v =>
                    (v || '').length >= 1 ||
                    this.$t('city_must_be_greater_than_1_characters'),
                v =>
                    (v || '').length <= 90 ||
                    this.$t('city_must_be_less_than_90_characters')
            ],
            addressRules: [v => !!v || this.$t('address_is_required')],
            phoneRules: [
                v => !!v || this.$t('phone_is_required'),
                v =>
                    /^[+]?(?:\(\d+(?:\.\d+)?\)|\d+(?:\.\d+)?)(?:[ -]?(?:\(\d+(?:\.\d+)?\)|\d+(?:\.\d+)?))*(?:[ ]?(?:x|ext)\.?[ ]?\d{1,5})?$/.test(
                        v
                    ) || this.$t('phone_number_must_be_digital'),
                v =>
                    (v || '').length === 15 || this.$t('phone_number_must_be_10_digits')
            ],
            materials: [
                { id: 1, value: this.$t('plastic') },
                { id: 2, value: this.$t('metal') }
            ],
            quality: [
                { id: 1, value: '30 ' + this.$t('micron') },
                { id: 2, value: '50 ' + this.$t('micron') },
                { id: 3, value: '100 ' + this.$t('micron') }
            ]
        };
    },
    computed: {
        hintZip() {
            if (this.zipRange !== '') {
                return this.$t('range') + ' ' + this.zipRange;
            } else {
                return this.$t('range') + ' ' + this.$t('not_determined');
            }
        },
        translate(item) {
            return this.$t(item);
        },
        formTitle() {
            return this.editedIndex === -1
                ? this.$t('new_printing_order')
                : this.$t('edit_printing_order');
        },
        cLang() {
            return this.$i18n.locale;
        },
        other() {
            if (typeof this.items.size_id === 'object') {
                return Number(this.items.size_id.id) === 0;
            } else {
                return Number(this.items.size_id) === 0;
            }
        },
        dynamicRules() {
            if (typeof this.items.size_id === 'object') {
                if (Number(this.items.size_id.id) === 0) {
                    return this.heightRules;
                } else {
                    return [];
                }
            } else {
                if (Number(this.items.size_id) === 0) {
                    return this.heightRules;
                } else {
                    return [];
                }
            }
        },
        checkZipCodeSelect() {
            return this.items.zip_code === '';
        },
        checkCountrySelect() {
            if (typeof this.items.country === 'object') {
                return this.items.country.country_name === '';
            } else {
                return this.items.country === '';
            }
        },
        countryInfo() {
            if (typeof this.items.country === 'object') {
                return this.items.country.country_name + ' (' + this.items.country.country_alpha2_code + ')';
            } else {
                return this.items.country;
            }
        }
    },
    watch: {
        cLang() {
            this.initialize();
        }
    },
    created() {
        this.initialize();
        this.getCountryList();
    },

    methods: {
        getHeaders() {
            return [
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('status'), value: 'status' },
                { text: this.$t('lang_name'), value: 'language' },
                { text: this.$t('materials'), value: 'materials' },
                { text: this.$t('name'), value: 'name' },
                { text: this.$t('address'), value: 'address' },
                {
                    text: this.$t('option'),
                    align: 'center',
                    value: 'options'
                },
                { text: this.$t('size'), value: 'sizes' },
                { text: this.$t('print_material'), value: 'print_material' },
                { text: this.$t('quality'), value: 'quality' },
                { text: this.$t('quantity'), value: 'quantity' },
                { text: this.$t('executor'), value: 'executor' },
                { text: this.$t('actions'), value: 'action', sortable: false }
            ];
        },
        add() {
            this.getStatuses();
            this.getExecutors();
            this.getLanguages();
            this.getCountryList();
            this.getCollectionSizes();
        },
        async initialize() {
            try {
                await axios
                    .get(api.path('printings') + '/' + this.cLang)
                    .then(req => {
                        this.printing_orders = req.data.data;
                        console.log(this.printing_orders);
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
        async getCountryList() {
            try {
                await axios
                    .get(api.path('countries') + '/' + this.cLang)
                    .then(req => {
                        this.countries = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchCountriesError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeCountriesError: ', e);
                return [];
            }
        },
        async getCollectionSizes() {
            try {
                await axios
                    .get(api.path('sizes') + '/' + this.cLang)
                    .then(req => {
                        this.sizes = req.data.data;
                    })
                    .catch(e => {
                        console.log('fetchSizesError: ', e);
                        return [];
                    });
            } catch (e) {
                console.log('initializeSizesError: ', e);
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
                        this.executors.push({
                            id: 0,
                            name: this.$t('not_assigned')
                        });
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
        customFilterCountry(item, queryText) {
            const textOne = item.country_name.toLowerCase();
            const textTwo = item.country_alpha2_code.toLowerCase();
            const searchText = queryText.toLowerCase();

            return (
                textOne.indexOf(searchText) > -1 ||
                textTwo.indexOf(searchText) > -1
            );
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
                                    this.$t('order_successfully_updated'),
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
            this.getCountryList();
            this.getCollectionSizes();
            try {
                await axios
                    .get(api.path('printing') + '/' + item)
                    .then(res => {
                        this.items = Object.assign({}, res.data.data);
                        this.items.hollow =
                            this.items.hollow === 1 ? '1' : null;
                        this.items.support =
                            this.items.support === 1 ? '1' : null;
                        this.items.post_processing =
                            this.items.post_processing === 1 ? '1' : null;
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
                        if (!lang && lang_id !== null) {
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
                            .delete(api.path('modeling') + '/' + item)
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

<style scoped>
.link {
    text-decoration: none;
}
</style>
