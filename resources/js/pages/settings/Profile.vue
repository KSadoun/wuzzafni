<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed<any>(() => page.props.auth.user);
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profile information"
            description="Update your profile details and account settings"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <!-- First Name and Last Name Grid Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="first_name">First name</Label>
                    <Input
                        id="first_name"
                        class="mt-1 block w-full"
                        name="first_name"
                        :default-value="user.first_name"
                        required
                        autocomplete="given-name"
                        placeholder="First name"
                    />
                    <InputError class="mt-2" :message="errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="last_name">Last name</Label>
                    <Input
                        id="last_name"
                        class="mt-1 block w-full"
                        name="last_name"
                        :default-value="user.last_name"
                        required
                        autocomplete="family-name"
                        placeholder="Last name"
                    />
                    <InputError class="mt-2" :message="errors.last_name" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <!-- Role-Specific Fields -->
            <template v-if="user.role === 'candidate'">
                <div class="border-t border-neutral-200 dark:border-neutral-800 pt-6 space-y-6">
                    <h3 class="text-sm font-semibold text-foreground">Candidate Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="current_position">Current Position</Label>
                            <Input
                                id="current_position"
                                name="current_position"
                                :default-value="user.candidate_profile?.current_position"
                                placeholder="e.g. Software Engineer"
                            />
                            <InputError class="mt-2" :message="errors.current_position" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">Phone Number</Label>
                            <Input
                                id="phone"
                                name="phone"
                                :default-value="user.candidate_profile?.phone"
                                placeholder="e.g. +123456789"
                            />
                            <InputError class="mt-2" :message="errors.phone" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="experience_level">Experience Level</Label>
                            <select
                                id="experience_level"
                                name="experience_level"
                                :value="user.candidate_profile?.experience_level || 'entry'"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800"
                            >
                                <option class="bg-background" value="entry">Entry Level</option>
                                <option class="bg-background" value="mid">Mid Level</option>
                                <option class="bg-background" value="senior">Senior Level</option>
                                <option class="bg-background" value="lead">Lead / Manager</option>
                            </select>
                            <InputError class="mt-2" :message="errors.experience_level" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="years_of_experience">Years of Experience</Label>
                            <Input
                                id="years_of_experience"
                                type="number"
                                name="years_of_experience"
                                :default-value="user.candidate_profile?.years_of_experience || 0"
                                min="0"
                            />
                            <InputError class="mt-2" :message="errors.years_of_experience" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="location">Location</Label>
                        <Input
                            id="location"
                            name="location"
                            :default-value="user.candidate_profile?.location"
                            placeholder="e.g. Cairo, Egypt"
                        />
                        <InputError class="mt-2" :message="errors.location" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="linkedin_url">LinkedIn URL</Label>
                            <Input
                                id="linkedin_url"
                                name="linkedin_url"
                                :default-value="user.candidate_profile?.linkedin_url"
                                placeholder="https://linkedin.com/in/username"
                            />
                            <InputError class="mt-2" :message="errors.linkedin_url" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="github_url">GitHub URL</Label>
                            <Input
                                id="github_url"
                                name="github_url"
                                :default-value="user.candidate_profile?.github_url"
                                placeholder="https://github.com/username"
                            />
                            <InputError class="mt-2" :message="errors.github_url" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="bio">Biography</Label>
                        <textarea
                            id="bio"
                            name="bio"
                            :value="user.candidate_profile?.bio"
                            rows="4"
                            placeholder="Describe your career history, goals, and experience..."
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800"
                        ></textarea>
                        <InputError class="mt-2" :message="errors.bio" />
                    </div>
                </div>
            </template>

            <template v-else-if="user.role === 'employer'">
                <div class="border-t border-neutral-200 dark:border-neutral-800 pt-6 space-y-6">
                    <h3 class="text-sm font-semibold text-foreground">Company Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="company_name">Company Name</Label>
                            <Input
                                id="company_name"
                                name="company_name"
                                :default-value="user.employer_profile?.company_name"
                                required
                            />
                            <InputError class="mt-2" :message="errors.company_name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="company_website">Company Website</Label>
                            <Input
                                id="company_website"
                                name="company_website"
                                :default-value="user.employer_profile?.company_website"
                                placeholder="https://example.com"
                            />
                            <InputError class="mt-2" :message="errors.company_website" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="field">Industry / Field</Label>
                            <Input
                                id="field"
                                name="field"
                                :default-value="user.employer_profile?.field"
                                placeholder="e.g. Software, Design"
                            />
                            <InputError class="mt-2" :message="errors.field" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="company_size">Company Size</Label>
                            <select
                                id="company_size"
                                name="company_size"
                                :value="user.employer_profile?.company_size || ''"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800"
                            >
                                <option class="bg-background" value="">Select size</option>
                                <option class="bg-background" value="1-10">1-10 Employees</option>
                                <option class="bg-background" value="11-50">11-50 Employees</option>
                                <option class="bg-background" value="51-200">51-200 Employees</option>
                                <option class="bg-background" value="201-500">201-500 Employees</option>
                                <option class="bg-background" value="500+">500+ Employees</option>
                            </select>
                            <InputError class="mt-2" :message="errors.company_size" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="location">Company Location</Label>
                        <Input
                            id="location"
                            name="location"
                            :default-value="user.employer_profile?.location"
                            placeholder="e.g. London, UK"
                        />
                        <InputError class="mt-2" :message="errors.location" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="company_description">Company Description</Label>
                        <textarea
                            id="company_description"
                            name="company_description"
                            :value="user.employer_profile?.company_description"
                            rows="4"
                            placeholder="Describe your company, products, and culture..."
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800"
                        ></textarea>
                        <InputError class="mt-2" :message="errors.company_description" />
                    </div>
                </div>
            </template>

            <div v-if="page.props.mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Your email address is unverified.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Click here to resend the verification email.
                    </Link>
                </p>

                <div
                    v-if="page.props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" class="cursor-pointer" data-test="update-profile-button"
                    >Save</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
