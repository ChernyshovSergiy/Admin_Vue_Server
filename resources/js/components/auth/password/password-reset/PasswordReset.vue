<template>
    <v-card>
        <v-toolbar dark color="#052238" flat>
            <v-toolbar-title>{{ $t('password_reset') }}</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
            <p>{{ $t('choose_new_password_below') }}</p>

            <password-reset-form @success="success" />
        </v-card-text>
    </v-card>
</template>

<script>
import PasswordResetForm from './PasswordResetForm';

export default {
    components: {
        PasswordResetForm
    },

    methods: {
        success(form) {
            this.$router.push({ name: 'login', query: { email: form.email } });
        }
    }
};
</script>
