<template>
    <div>
        <h2 class="mb-4 primary--text headline">
            {{ $t('your_profile') }}
        </h2>

        <v-card>
            <v-card-text>
                <v-text-field
                    v-model="user.name"
                    :label="`${$t('name')}`"
                    filled
                    readonly
                    hide-details
                    class="mb-4"
                />

                <v-text-field
                    v-model="user.email"
                    :label="`${$t('email')}`"
                    filled
                    readonly
                    hide-details
                />
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    data: () => ({
        user: {
            name: null,
            email: null
        }
    }),

    computed: mapGetters({
        auth: 'auth/user'
    }),

    mounted() {
        this.user = Object.assign(this.user, this.auth);
    }
};
</script>
