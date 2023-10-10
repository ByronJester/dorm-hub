<script setup>
import TenantLayout from '@/Layouts/AuthenticatedLayout.vue';
import OtherLayout from '@/Layouts/SidebarLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import IncomeInformation from './Partials/IncomeInformation.vue';
import { Head } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profile" />

    <TenantLayout v-if="user.user_type == 'tenant'">
        <div class="w-full pt-10">
            <button class="bg-cyan-500 float-right py-2 px-4 rounded-md text-xs mr-2 md:mr-32"
            >
                <Link
                    class="block w-full text-xs"
                    :href="route('profile.change.password')"
                >
                    Change Password
                </Link>
            </button>
        </div>

        <div class="py-12 main">
            <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mx-2 md:mx-24">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="w-full"
                    />
                </div>
            </div>

            <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6 mt-5" v-if="user.user_type == 'tenant'">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mx-2 md:mx-24">
                    <IncomeInformation />
                </div>
            </div>
        </div>
    </TenantLayout>

    <OtherLayout v-else>
        <div class="w-full main">
            <div class="w-full pt-5 md:pt-10">
                <button class="bg-cyan-500 float-right py-2 px-4 rounded-md text-xs mr-2 md:mr-32 mt-2"
                >
                    <Link
                        class="block w-full text-xs"
                        :href="route('profile.change.password')"
                    >
                        Change Password
                    </Link>
                </button>
            </div>

            <div class="py-12">
                <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mx-2 md:mx-24">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="w-full"
                        />
                    </div>
                </div>

                <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6 mt-5" v-if="user.user_type == 'tenant'">
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mx-2 md:mx-24">
                        <IncomeInformation />
                    </div>
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
