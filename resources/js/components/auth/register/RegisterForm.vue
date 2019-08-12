<template>
    <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit">
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

        <v-text-field
            v-model="form.password"
            :label="`${$t(labels.password)}`"
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
            :label="`${$t(labels.password_confirmation)}`"
            :type="passwordHidden ? 'password' : 'text'"
            :error-messages="errors.password_confirmation"
            :disabled="loading"
            :rules="[rules.required('password_confirmation')]"
        />

        <v-layout row class="mt-4 mx-0">
            <v-spacer />

            <v-btn
                text
                :disabled="loading"
                :to="{ name: 'login' }"
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
                {{ $t('register') }}
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

        form: {
            name: null,
            email: null,
            password: null,
            password_confirmation: null
        }
    }),

    methods: {
        submit() {
            if (this.$refs.form.validate()) {
                this.loading = true;
                axios
                    .post(api.path('register'), this.form)
                    .then(res => {
                        this.$toast.success(
                            this.$t('your_have_been_successfully_registered')
                        );
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
