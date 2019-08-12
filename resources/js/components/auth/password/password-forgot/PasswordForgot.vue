<template>
    <v-card>
        <v-toolbar dark color="#052238" flat>
            <v-toolbar-title>{{ $t('password_reset') }}</v-toolbar-title>
        </v-toolbar>
        <v-card-text>
            <p>{{ $t('enter_your_email_address') }}</p>

            <password-forgot-form @success="success" />
        </v-card-text>
    </v-card>
</template>

<script>
import PasswordForgotForm from './PasswordForgotForm';

export default {
    components: {
        PasswordForgotForm
    },

    methods: {
        success(data) {
            this.$router.push({ name: 'login' });
        }
    }
};
</script>
