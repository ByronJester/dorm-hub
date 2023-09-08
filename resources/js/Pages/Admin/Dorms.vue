<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthenticatedLayout>
        <div style="background-color: #EB984E;" class="main">
            Admin
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 92vh;
    }
</style>
