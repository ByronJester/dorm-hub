<script setup>
import TenantLayout from "@/Layouts/SidebarLayout.vue";
import OtherLayout from "@/Layouts/SidebarLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import IncomeInformation from "./Partials/IncomeInformation.vue";
import AddProfile from "./Partials/AddProfile.vue";
import { Head } from "@inertiajs/vue3";
import { Link, usePage } from "@inertiajs/vue3";

const user = usePage().props.auth.user;
const profile = usePage().props.profile;

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <TenantLayout v-if="user.user_type == 'tenant'">
        <div class="p-4 mt-16 lg:ml-64">
            <div class="min-w-screen
                        2xl:mx-40
                        ">
            
            <section class="mb-6 flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3"
                        ><svg
                            viewBox="0 0 24 24"
                            width="24"
                            height="24"
                            class="inline-block"
                        >
                            <path
                                fill="currentColor"
                                d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"
                            ></path>
                        </svg>
                    </span>
                    <h1 class="text-3xl leading-tight">Profile</h1>
                </div>
                <button
                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded-full border-gray-800 dark:border-white ring-gray-300 dark:ring-gray-400 bg-gray-800 text-white dark:bg-white dark:text-black hover:bg-gray-700 hover:dark:bg-orange-400 hover:dark:text-white text-sm px-3 py-1"
                    target="_blank"
                    disabled="false"
                >
                    <Link
                        class="block w-full text-xs"
                        :href="route('profile.change.password')"
                    >
                        Change Password
                    </Link>
                </button>
            </section>

            <div>
                    <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="w-full"
                        />
  
                        

                <div
                    v-if="user.user_type == 'tenant'"
                >
                   
                      <AddProfile :profile="profile" />
                  
                </div>
            </div>
                    
                    
        </div>
        </div>
    </TenantLayout>

    <OtherLayout v-else>
        <div class="p-4 mt-16 lg:ml-64">
            <section class="mb-6 flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3"
                        ><svg
                            viewBox="0 0 24 24"
                            width="24"
                            height="24"
                            class="inline-block"
                        >
                            <path
                                fill="currentColor"
                                d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"
                            ></path>
                        </svg>
                    </span>
                    <h1 class="text-3xl leading-tight">Profile</h1>
                </div>
                <button
                    class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded-full border-gray-800 dark:border-white ring-gray-300 dark:ring-gray-400 bg-gray-800 text-white dark:bg-white dark:text-black hover:bg-gray-700 hover:dark:bg-orange-400 hover:dark:text-white text-sm px-3 py-1"
                    target="_blank"
                    disabled="false"
                >
                    <Link
                        class="block w-full text-xs"
                        :href="route('profile.change.password')"
                    >
                        Change Password
                    </Link>
                </button>
            </section>

            <div>
                    <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="w-full"
                        />
  
            </div>
        </div>
    </OtherLayout>
</template>

<style>
.main {
    height: 100%;
    min-height: 92vh;
    background-color: #e5e8e8;
}
</style>
