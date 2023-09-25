<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage, useForm, router } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, computed } from 'vue';
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
    },
    setup(){
        const page = usePage()
        const user = computed(() => page.props.auth.user);
        const payments = ref([])
        const application = ref({})
        const owner = ref({})
        const isMobileView = ref(false)
        isMobileView.value = screen.width < 600;
        const paymentMethod = ref()

        onMounted(() => {
            payments.value = page.props.payments
            application.value = page.props.application
            owner.value = page.props.owner
        });

        const pay = (id, method) => {
            swal({
                title: `Are you sure to pay rent?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(){
                axios.post(route('pay.rent', id), {method: method})
                    .then(response => {
                        window.location.href = response.data.redirect.checkout_url
                        // swal("Success!", `Pay rent successfully`, "success");

                        // setTimeout(function () {
                        //     location.reload()
                        // }, 1500);
                    })
                    .catch(error => {
                        console.log(error)
                    })
            });
        }

        return {
            user,
            payments,
            application,
            owner,
            isMobileView,
            paymentMethod,
            pay
        }
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="main w-full">
            <div class="w-full md:flex md:justify-center md:items-center" v-if="payments.length > 0">
                <div v-if="payments.length > 0"
                    :style="{width: isMobileView ? '100vw' : '35vw'}"
                    class="px-5 py-5 md:px-0 md:py-0"
                >
                    <div class="w-full flex justify-center items-center rounded-sm mt-5"
                        v-for="payment in payments" :key="payment.id"
                        style="height: 350px; 	background-color: rgb(251 146 60)"

                    >
                        <div class="w-full flex flex-col bg-white rounded-lg"
                            style="width: 90%;"
                        >
                            <div class="w-full">
                                <p class="text-center font-bold mt-10"
                                    :style="{'font-size': isMobileView ? '15px': '20px'}"
                                >
                                    {{ owner.name }}
                                </p>

                                <p class="text-center font-bold"
                                    :style="{'font-size': isMobileView ? '12px': '15px'}"
                                >
                                    {{ owner.phone_number }}
                                </p>

                                <p class="text-center font-bold my-8"
                                    :style="{'font-size': isMobileView ? '15px': '22px'}"
                                >
                                 ₱{{ !payment.partial ? parseFloat(payment.amount_to_pay - payment.amount_paid).toFixed(2) :   parseFloat(payment.partial).toFixed(2) }}
                                </p>
                            </div>

                            <div class="w-full flex justify-center items-center mb-2">
                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                    @click="pay(payment.id, 'GCash Payment')"
                                >
                                    Pay Via GCash
                                </button>

                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                    @click="pay(payment.id, 'Bank Payment')"
                                >
                                    Pay Via Bank
                                </button>

                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs"
                                    @click="pay(payment.id, 'Cash Payment')"
                                >
                                    Pay Cash
                                </button>
                            </div>

                            <div class="w-full mb-2">
                                <p class="text-center font-bold mt-3"
                                    :style="{'font-size': isMobileView ? '10px': '10px'}"
                                >
                                    {{ payment.display_date }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="w-full flex justify-center items-center" v-if="payments.length == 0">
                <div class=""
                    :style="{width: isMobileView ? '100vw' : '50vw'}"
                >
                    <div class="w-full px-2 rounded-lg text-center flex justify-center items-center bg-gray-200"
                        :style="{height: isMobileView ? '200px': '500px'}"

                    >
                        <p class="font-bold" :style="{'font-size': isMobileView ? '16px': '25px'}">
                            There's no payment(s) in your account.
                        </p>
                    </div>
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
</style>
