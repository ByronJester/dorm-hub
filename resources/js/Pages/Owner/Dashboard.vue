<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';

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

            console.log(totalApplications)

            const moneyFormat = (amount) => {
                amount = parseFloat(amount).toFixed(2)

                return '₱ ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');;
            }

            return {
                user,
                paidAmount,
                unpaidAmount,
                totalTenants,
                totalApplications,
                moneyFormat
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            <div class="w-full px-5 flex flex-col md:flex-row pt-5">
                <div class="w-full bg-gray-300 mx-0 md:mx-1 mt-2 md:mt-0">
                    <p class="text-center text-2xl md:text-3xl pt-5 md:pt-10 pb-3">
                        {{ moneyFormat(paidAmount) }}
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
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }

    ::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* make scrollbar transparent */
    }

</style>
