<template>
    <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit">
        <v-text-field
            v-model="form.password"
            :label="`${$t('password')}`"
            :append-icon="passwordHidden ? 'visibility_off' : 'visibility'"
            :type="passwordHidden ? 'password' : 'text'"
            :error-messages="errors.password"
            :disabled="loading"
            :rules="[rules.required('password')]"
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
            :rules="[rules.required('password_confirmation')]"
        />

        <v-layout class="mt-4 mx-0">
            <v-spacer />

            <v-btn
                type="submit"
                :loading="loading"
                :disabled="loading || !valid"
                color="#052238"
                dark
            >
                {{ $t('set_new_password') }}
            </v-btn>
        </v-layout>
    </v-form>
</template>

<script>
import axios from 'axios';
import { api } from '~/config';
import Form from '~/mixins/form';

export default {
    mixins: [Form],

    data: () => ({
        passwordHidden: true,

        labels: {
            password: 'password',
            password_confirmation: 'password Confirmation'
        },

        form: {
            token: null,
            email: null,
            password: null,
            password_confirmation: null
        },
        valid: true,
        loading: false,
    }),

    created() {
        this.form.email = this.$route.query.email;
        this.form.token = this.$route.params.token;
    },

    methods: {
        submit() {
            if (this.$refs.form.validate()) {
                this.loading = true;
                axios
                    .post(api.path('password.reset'), this.form)
                    .then(res => {
                        this.$toast.success(
                            this.$t('your_password_has_been_reset')
                        );
                        this.$emit('success', this.form);
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
