<script setup>
import TenantLayout from '@/Layouts/AuthenticatedLayout.vue';
import OtherLayout from '@/Layouts/SidebarLayout.vue';
import Settings from './Partials/PaymentSettings.vue';
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
        <div class="p-4 mt-16 md:ml-64">
          <Settings class="w-full" />
        </div>
    </TenantLayout>

    <OtherLayout v-else>
        <div class="p-4 mt-16 md:ml-64">
          <Settings class="w-full" />
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
