<template>
    <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit">
        <v-text-field
            v-model="form.email"
            :label="`${$t('email')}`"
            type="email"
            :error-messages="errors.email"
            :rules="[rules.required('email')]"
            :disabled="loading"
            prepend-icon="person"
            autocomplete="email"
        />

        <v-text-field
            v-model="form.password"
            :label="`${$t('password')}`"
            :append-icon="passwordHidden ? 'visibility_off' : 'visibility'"
            :type="passwordHidden ? 'password' : 'text'"
            :error-messages="errors.password"
            :disabled="loading"
            :rules="[rules.required('password')]"
            prepend-icon="lock"
            autocomplete="password"
            @click:append="() => (passwordHidden = !passwordHidden)"
        />

        <v-layout class="mt-4 mx-0">
            <v-spacer />

            <v-btn
                text
                :disabled="loading"
                :to="{ name: 'forgot', query: { email: form.email } }"
                color="grey darken-2"
            >
                {{ $t('forgot_password') }}
            </v-btn>

            <v-btn
                type="submit"
                :loading="loading"
                :disabled="loading || !valid"
                color="#052238"
                class="ml-4"
                dark
            >
                {{ $t('login') }}
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
        valid: true,
        loading: false,

        form: {
            email: null,
            password: null,
            cLang: this.$i18n.locale
        }
    }),

    created() {
        this.form.email = this.$route.query.email || null;
    },

    methods: {
        submit() {
            if (this.$refs.form.validate()) {
                this.loading = true;

                axios
                    .post(api.path('login'), this.form)
                    .then(res => {
                        this.$toast.success(this.$t('welcome_back'), {
                            timeout: 10000,
                            icon: 'done_outline',
                            dismissable: false,
                            showClose: true
                        });
                        this.$emit('success', res.data);
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
