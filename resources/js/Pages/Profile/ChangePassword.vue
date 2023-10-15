<script setup>
import TenantLayout from '@/Layouts/AuthenticatedLayout.vue';
import OtherLayout from '@/Layouts/SidebarLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profile" />

    <TenantLayout v-if="user.user_type == 'tenant'">
        <div class="py-12 main">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </TenantLayout>

    <OtherLayout v-else>
        <div class="py-12 main">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </OtherLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }
</style>
