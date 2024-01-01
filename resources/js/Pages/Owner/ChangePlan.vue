<script>
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import DataTable from 'primevue/datatable';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { ref, onMounted, watch } from "vue";

import { usePage, useForm, router } from "@inertiajs/vue3";

import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import Image from 'primevue/image';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import axios from "axios";
export default {
    components: {
        SidebarLayout,
        DataTable,
        Button,
        Tag,
        Column,
        Row,
        Image,
        InputLabel,
        TextInput,
        ConfirmDialog,
        router

    },
    setup() {
        const loading = ref(false);
        const termsAndCondition = ref([]);
        const confirm = useConfirm();
        const data = new FormData();
        const toast = useToast();
        const page = usePage();
        const filters = ref();
        const getSeverity = (status) => {
            switch (status) {
                case 'decline':
                    return 'danger';

                case 'approved':
                    return 'success';

                case 'pending':
                    return 'warning';

            }
        };

        const initFilters = () => {
            filters.value = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                user_type: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                phone_number: { value: null, matchMode: FilterMatchMode.IN },
                created_at: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },

            };
        };

        initFilters();

        const clearFilter = () => {
            initFilters();
        };

        const formatDate = (value) => {
            // Check if value is a string and convert it to a Date object
            const date = typeof value === 'string' ? new Date(value) : value;

            // Check if date is a valid Date object
            if (isNaN(date.getTime())) {
                // If not a valid Date, you can handle it according to your requirements
                return "Invalid Date"; // or return value.toString() or any other representation
            }

            // If it's a valid Date object, format it
            return date.toLocaleDateString('en-US', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        };
        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const payNow = (arg) => {
            axios
                .post(route("payment.mark-as-paid"), arg)
                .then((response) => {
                    if (!!response.data) {
                        window.location.href = response.data
                    }

                })
                .catch((error) => {
                    toast.add({ severity: 'error', summary: 'Warning', detail: error, life: 3000 });
                    // errors.value = error.response.data.errors;
                });
        }


        const subs = page.props.subscriptionPayments;
        const subscriptions = [
            { label: 'Starter', value: 'starter', description: '3 Dorm Listings', price: 300 },
            { label: 'Basic', value: 'basic', description: '5 Dorm Listings', price: 500 },
            { label: 'Plus', value: 'plus', description: 'Unlimited Dorm Listings', price: 1000 },
        ];

        let subscriptionArray = [];

        subscriptions.filter((x) => {
            if (x['value'] === subs[0]['subscription']) {
                subscriptionArray = x;
            }

        });

        const selectedSubscription = ref(subscriptionArray);

        // Watch for changes in the selected subscription
        watch(() => selectedSubscription.value, (newValue) => {
            console.log(`Selected subscription changed to: ${newValue.label}`);
        });

        const confirmPayment = () => {

            if (termsAndCondition.value.length == 2 && subs[0]['subscription'] !== selectedSubscription.value.value) {
                confirm.require({
                    message: 'Are you sure you want to proceed?',
                    header: 'Confirmation',
                    icon: 'fa-solid fa-triangle-exclamation',
                    accept: () => {
                        loading.value = true;

                        // Subscription Table
                        data.append("subscription", selectedSubscription.value.value);
                        subs.filter((x) => {
                            if (x['is_active'] == 1) {
                                data.append("subscriptionPaymentId", x['id']);
                            }
                        })
                        // const selectedServices = services.value.map(service => service.name);
                        // const selectedAmenities = amenities.value.map(amenity => amenity.name);

                        // if (!!id.value) {
                        //     data.append("id", id.value);
                        // }

                        axios
                            .post(route("update.subscription"), data)
                            .then((response) => {
                                loading.value = false;
                                window.location.href = response.data
                            })
                            .catch((error) => {
                                // errors.value = error.response.data.errors;
                                // console.log(data);
                                // console.log(error);
                                // loading.value = false;
                            });
                    },
                    reject: () => {

                    }
                });
            }
        }


        return {
            filters,
            getSeverity,
            clearFilter,
            subs,
            moneyFormat,
            payNow,
            subscriptions,
            selectedSubscription,
            confirmPayment,
            termsAndCondition
        }
    }
}
</script>
<template>
    <SidebarLayout>
        <div class="w-full">
            <div clas="w-full grid grid-cols-3 gap-5">
                <div className=" max-w-screen-lg mx-auto ">
                    <div>
                        <p class="text-3xl font-extrabold mt-5 text-black">Select Subscription</p>
                        <div class="w-full grid grid-cols-1 md:grid-cols-5 gap-10 py-5">
                            <ul class="col-span-3 md:col-span-2 ">
                                <li v-for="subscription in subscriptions" :key="subscription.value" class="mt-4">
                                    <input type="radio" :id="subscription.value" name="subscription" :value="subscription"
                                        class="hidden peer" v-model="selectedSubscription" required>
                                    <label :for="subscription.value"
                                        class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-orange-600 peer-checked:text-orange-400 hover:text-gray-600 hover:bg-gray-100 ">
                                        <div class="block">
                                            <div class="w-full text-lg font-semibold">{{ subscription.label }}</div>
                                            <div class="w-full">{{ subscription.description }}</div>
                                            <div class="w-full">{{ moneyFormat(subscription.price) }} /month</div>
                                        </div>
                                        <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                                        </svg>
                                    </label>
                                </li>
                            </ul>
                            <div class="col-span-3 mt-4">
                                <div>
                                    <div class=" bg-white border rounded-lg shadow px-4 pt-8 lg:mt-0">
                                        <p class="text-xl font-medium">Payment Details</p>
                                        <p class="text-gray-400">Complete by providing your payment details.</p>
                                        <!--Payment Method-->
                                        <div class="w-full">
                                            <label class="text-sm font-semibold">Name:</label>
                                            <TextInput type="text" class="placeholder:text-gray-400 w-full"
                                                :model-value="subs[0]['user']['name']" disabled />
                                        </div>
                                        <!--Total-->
                                        <div class="mt-6 border-t border-b py-2">
                                            <p class="text-md font-bold text-gray-900">Subscription</p>
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">{{ selectedSubscription ?
                                                    selectedSubscription.label : 'Select a subscription' }}</p>
                                                <p class="font-semibold text-gray-900">{{
                                                    moneyFormat(selectedSubscription.value ? selectedSubscription.price : 0)
                                                }} /month</p>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">Total</p>
                                            <p class="text-2xl font-semibold text-gray-900">{{
                                                moneyFormat(selectedSubscription ? selectedSubscription.price : 0) }}</p>
                                        </div>
                                        <hr class="my-4" />
                                        <div class="w-full mt-2">
                                            <input type="checkbox"
                                                value="I guarantee that all details provided are accurate and true."
                                                v-model="termsAndCondition" />
                                            <label class="ml-2 text-sm">* I guarantee that all details provided are accurate
                                                and true.</label>
                                        </div>

                                        <div class="w-full mt-2">
                                            <input type="checkbox" value="I agree with the terms and condition."
                                                v-model="termsAndCondition" />
                                            <label class="ml-2 text-sm">* I agree with the
                                                <span class="text-orange-400 cursor-pointer hover:underline">terms and
                                                    condition</span></label>
                                        </div>
                                        <Toast />
                                        <ConfirmDialog />
                                        <button @click="confirmPayment()" :class="{
                                            'cursor-not-allowed bg-orange-200':
                                                loading || termsAndCondition.length < 2,
                                            'bg-orange-500 text-white':
                                                !loading && termsAndCondition.length === 2,
                                        }"
                                            class="mt-4 mb-8 w-full rounded-md bg-orange-400 px-6 py-3 font-medium text-white">
                                            Proceed to Payment
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center mt-5 justify-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <p class="text-gray-800 font-bold">Payment processed by:</p>
                                        <Image src="/images/xenditlogo.png" alt="xendit logo" width="220" />
                                    </div>
                                    <div>
                                        <Image src="/images/credicardlogo.png" alt="Credit card logo" width="200" />
                                    </div>
                                    <div class="flex flex-row mt-3 gap-3">
                                        <Image src="/images/paymayalogo.png" alt="Maya logo" width="100" />
                                        <Image src="/images/grablogo.png" alt="Grab pay logo" width="50" />
                                        <Image src="/images/gcashlogo.png" alt="Gcash logo" width="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </SidebarLayout>
</template>
