<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});

const role = ref('candidate');
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <!-- First Name and Last Name Row -->
            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="first_name">First name</Label>
                    <Input
                        id="first_name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="given-name"
                        name="first_name"
                        placeholder="First name"
                    />
                    <InputError :message="errors.first_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="last_name">Last name</Label>
                    <Input
                        id="last_name"
                        type="text"
                        required
                        :tabindex="1"
                        autocomplete="family-name"
                        name="last_name"
                        placeholder="Last name"
                    />
                    <InputError :message="errors.last_name" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Role Selector Cards -->
            <div class="grid gap-2">
                <Label>Register as a:</Label>
                <input type="hidden" name="role" :value="role" />
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div
                        class="flex items-center gap-3 p-4 rounded-lg border cursor-pointer select-none transition-all duration-200"
                        :class="role === 'candidate' ? 'border-primary bg-primary/5 ring-1 ring-primary/30' : 'border-neutral-200 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900/50'"
                        @click="role = 'candidate'"
                    >
                        <span class="text-2xl">👤</span>
                        <div>
                            <div class="font-medium text-sm">Candidate</div>
                            <div class="text-xs text-muted-foreground">Applying for jobs</div>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 p-4 rounded-lg border cursor-pointer select-none transition-all duration-200"
                        :class="role === 'employer' ? 'border-primary bg-primary/5 ring-1 ring-primary/30' : 'border-neutral-200 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900/50'"
                        @click="role = 'employer'"
                    >
                        <span class="text-2xl">💼</span>
                        <div>
                            <div class="font-medium text-sm">Employer</div>
                            <div class="text-xs text-muted-foreground">Hiring top talent</div>
                        </div>
                    </div>
                </div>
                <InputError :message="errors.role" />
            </div>

            <!-- Dynamic Company Name Field for Employers -->
            <div v-if="role === 'employer'" class="grid gap-2 animate-fade-in">
                <Label for="company_name">Company name</Label>
                <Input
                    id="company_name"
                    type="text"
                    required
                    name="company_name"
                    placeholder="Acme Corp"
                />
                <InputError :message="errors.company_name" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full cursor-pointer"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Create account
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Log in</TextLink
            >
        </div>
    </Form>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

