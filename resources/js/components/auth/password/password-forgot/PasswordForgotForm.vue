<template>
    <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit">
        <v-text-field
            v-model="form.email"
            :label="labels.email"
            type="email"
            :error-messages="errors.email"
            :rules="[rules.required('email')]"
            :disabled="loading"
            autocomplete="email"
        />

        <v-layout class="mt-4 mx-0">
            <v-spacer />

            <v-btn
                text
                :disabled="loading"
                :to="{ name: 'login', query: { email: form.email } }"
                color="grey darken-2"
                exact
            >
                {{ $t('back_to_login') }}
            </v-btn>

            <v-btn
                type="submit"
                :loading="loading"
                :disabled="loading || !valid"
                color="#052238"
                class="ml-4"
                dark
            >
                {{ $t('get_password') }}
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
        form: {
            email: null
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
                    .post(api.path('password.forgot'), this.form)
                    .then(res => {
                        this.$toast.info(this.$t('email_with_password_reset'));
                        this.$emit('success');
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
