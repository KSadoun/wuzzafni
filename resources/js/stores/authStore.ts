import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<Record<string, any> | null>(null);

    function setUser(userData: Record<string, any> | null) {
        user.value = userData;
    }

    return {
        user,
        setUser
    };
});
