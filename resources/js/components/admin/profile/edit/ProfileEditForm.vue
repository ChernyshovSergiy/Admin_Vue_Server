<template>
    <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit">
        <v-card>
            <v-card-text>
                <v-text-field
                    v-model="form.name"
                    :label="labels.name"
                    :error-messages="errors.name"
                    :rules="[rules.required('name')]"
                    :disabled="loading"
                />

                <v-text-field
                    v-model="form.email"
                    :label="labels.email"
                    type="email"
                    :error-messages="errors.email"
                    :rules="[rules.required('email')]"
                    :disabled="loading"
                    autocomplete="email"
                />
            </v-card-text>
        </v-card>

        <h3 class="headline mb-4 mt-12">
            {{ $t('password') }}
        </h3>

        <v-card>
            <v-card-text>
                <v-text-field
                    v-model="form.password"
                    :label="`${$t('password')}`"
                    :append-icon="
                        passwordHidden ? 'visibility_off' : 'visibility'
                    "
                    :type="passwordHidden ? 'password' : 'text'"
                    :error-messages="errors.password"
                    :disabled="loading"
                    :hint="`${$t('at_least_6_characters')}`"
                    autocomplete="password"
                    @click:append="() => (passwordHidden = !passwordHidden)"
                />

                <v-text-field
                    v-model="form.password_confirmation"
                    :label="`${$t('password_confirmation')}`"
                    :type="passwordHidden ? 'password' : 'text'"
                    :error-messages="errors.password_confirmation"
                    :disabled="loading"
                    autocomplete="new-password"
                />
            </v-card-text>
        </v-card>

        <v-layout mt-12 mx-0>
            <v-spacer />

            <v-btn
                text
                :disabled="loading"
                :to="{ name: 'profile' }"
                color="grey darken-2"
                exact
            >
                {{ $t('cancel') }}
            </v-btn>

            <v-btn
                type="submit"
                :loading="loading"
                :disabled="loading"
                color="#052238"
                class="ml-4"
                dark
            >
                {{ $t('save') }}
            </v-btn>
        </v-layout>
    </v-form>
</template>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import { api } from '~/config';
import Form from '~/mixins/form';

export default {
    mixins: [Form],

    data: () => ({
        passwordHidden: true,
        loading: false,

        label: {
            password: 'password',
            password_confirmation: 'password_confirmation'
        },

        form: {
            name: null,
            email: null,
            password: null,
            password_confirmation: null
        }
    }),

    computed: mapGetters({
        auth: 'auth/user'
    }),

    mounted() {
        this.form = Object.assign(this.form, this.auth);
    },

    methods: {
        submit() {
            if (this.$refs.form.validate()) {
                this.loading = true;
                console.log(this.form);
                console.log(api.path('profile'));
                axios
                    .put(api.path('profile'), this.form)
                    .then(res => {
                        this.$toast.success(
                            this.$t('your_profile_successfully_updated'),
                            {
                                timeout: 10000,
                                icon: 'done_outline',
                                dismissable: false,
                                showClose: true
                            }
                        );
                        this.$emit('success', res.data);
                        console.log(res.data);
                    })
                    .catch(err => {
                        this.handleErrors(err.response.data.errors);
                    })
                    .then(() => {
                        this.loading = false;
                    });
            }
        }
    }
};
</script>
