<template>
    <v-flex sm8 md6 lg4>
        <v-card>
            <v-toolbar dark color="#052238" flat>
                <v-toolbar-title>{{ $t('login') }}</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <login-form @success="success" />
            </v-card-text>
        </v-card>

        <div class="text-center mt-4">
            {{ $t('not_have_account') }}
            <router-link :to="{ name: 'register' }">
                {{ $t('register') }}
            </router-link>
        </div>
    </v-flex>
</template>

<script>
import LoginForm from './LoginForm';

export default {
    components: {
        LoginForm
    },

    methods: {
        success(data) {
            this.$store.dispatch('auth/saveToken', data);
            this.$store.dispatch('auth/setUser', data);
            this.$router.push({ name: 'index' });
        }
    }
};
</script>
