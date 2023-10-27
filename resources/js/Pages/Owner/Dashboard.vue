<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import { Head } from "@inertiajs/vue3";
import { VueGoodTable } from "vue-good-table-next";
import { ref, computed, reactive, watch, onMounted, defineProps } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import axios from "axios";

export default {
    components: {
        AuthenticatedLayout,
    },
    setup() {
        const page = usePage();

        const user = computed(() => page.props.auth.user);

        const paidAmount = page.props.paidAmount;
        const unpaidAmount = page.props.unpaidAmount;
        const totalTenants = page.props.totalTenants;
        const totalApplications = page.props.totalApplications;

        console.log(totalApplications);

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        return {
            user,
            paidAmount,
            unpaidAmount,
            totalTenants,
            totalApplications,
            moneyFormat,
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 lg:ml-64">
            
            <div class="flex items-center justify-start mt-16">
                <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                    <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block"><path fill="currentColor" d="M3,14L3.5,14.07L8.07,9.5C7.89,8.85 8.06,8.11 8.59,7.59C9.37,6.8 10.63,6.8 11.41,7.59C11.94,8.11 12.11,8.85 11.93,9.5L14.5,12.07L15,12C15.18,12 15.35,12 15.5,12.07L19.07,8.5C19,8.35 19,8.18 19,8A2,2 0 0,1 21,6A2,2 0 0,1 23,8A2,2 0 0,1 21,10C20.82,10 20.65,10 20.5,9.93L16.93,13.5C17,13.65 17,13.82 17,14A2,2 0 0,1 15,16A2,2 0 0,1 13,14L13.07,13.5L10.5,10.93C10.18,11 9.82,11 9.5,10.93L4.93,15.5L5,16A2,2 0 0,1 3,18A2,2 0 0,1 1,16A2,2 0 0,1 3,14Z"></path></svg>
                </span>
                <h3 class="text-3xl">Overview</h3>
                <a :href="route('owner.addDorm')" class="ml-80 bg-slate-500 p-6 text-white">
                    Go to add dorm
                </a>
            </div>
            
            <div
                class="grid grid-cols-2 lg:grid-cols-4 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{ moneyFormat(paidAmount) }}
                        </p>
                        <p class="text-xs">
                            TOTAL PAID RENTAL AMOUNT FOR THIS MONTH
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{ moneyFormat(unpaidAmount) }}
                        </p>
                        <p class="text-xs">
                            TOTAL UNPAID RENTAL AMOUNT FOR THIS MONTH
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{ totalTenants }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF TENANTS</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{ totalApplications }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF APPLICANTS</p>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div class="main">
            <div class="w-full px-5 flex flex-col md:flex-row pt-5">
                <div class="w-full bg-gray-300 mx-0 md:mx-1 mt-2 md:mt-0">
                    <p class="text-center text-2xl md:text-3xl pt-5 md:pt-10 pb-3">
                        
                    </p>

                    <p class="text-center text-xs my-3">
                        TOTAL PAID RENTAL AMOUNT FOR THIS MONTH
                    </p>
                </div>

                <div class="w-full bg-gray-300 mx-0 md:mx-1 mt-2 md:mt-0">
                    <p class="text-center text-2xl md:text-3xl pt-5 md:pt-10 pb-3">
                        {{ moneyFormat(unpaidAmount) }}
                    </p>

                    <p class="text-center text-xs my-3">
                        TOTAL UNPAID RENTAL AMOUNT FOR THIS MONTH
                    </p>
                </div>
            </div>

            <div class="w-full px-5 flex flex-col md:flex-row pt-5">
                <div class="w-full bg-gray-300 mx-0 md:mx-1 mt-2 md:mt-0">
                    <p class="text-center text-2xl md:text-3xl pt-5 md:pt-10 pb-3">
                        {{ totalTenants }}
                    </p>

                    <p class="text-center text-xs my-3">
                        TOTAL NO. OF TENANTS
                    </p>
                </div>

                <div class="w-full bg-gray-300 mx-0 md:mx-1 mt-2 md:mt-0">
                    <p class="text-center text-2xl md:text-3xl pt-5 md:pt-10 pb-3">
                        {{ totalApplications }}
                    </p>

                    <p class="text-center text-xs my-3">
                        TOTAL UNPAID RENTAL AMOUNT FOR THIS MONTH
                    </p>
                </div>
            </div>
        </div>-->
    </AuthenticatedLayout>
</template>

<style>
.main {
    height: 100%;
    min-height: 92vh;
    background-color: #fdffff;
}

::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* make scrollbar transparent */
}
</style>
