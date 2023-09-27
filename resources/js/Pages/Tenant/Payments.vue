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
        const methods = ref([])
        const isMobileView = ref(false)
        isMobileView.value = screen.width < 600;
        const receipt = ref(null);
        const payment_id = ref()
        const imageError = ref(null)

        onMounted(() => {
            payments.value = page.props.payments
            application.value = page.props.application
            owner.value = page.props.owner
            methods.value = page.props.methods
        });

        const pay = (id, method) => {
            if(method == 'GCash Payment') {
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
                        })
                        .catch(error => {
                            console.log(error)
                        })
                });
            }

            if(method == 'Bank Payment') {
                if(receipt.value == null) {
                    imageError.value = 'Bank transfer receipt is required';

                    return;
                }

                imageError.value = null

                swal({
                    title: `Are you sure to upload this receipt?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('pay.rent', id), {method: method, receipt: receipt.value})
                        .then(response => {
                            swal("Success!", `Please wait for the dorm owner to verify you receipt.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 1500);
                        })
                        .catch(error => {
                            console.log(error)
                        })
                });
            }

        }

        const openModal = (arg) => {
            payment_id.value = arg

            var modal = document.getElementById("bankModal");

            modal.style.display = "block";

        }

        const closeModal = () => {
            var modal = document.getElementById("bankModal");

            modal.style.display = "none";
        }

        const imageClick = () => {
            document.getElementById('bank_receipt').click()
        }

        const imageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = e => {
                receipt.value = e.target.result
            }
        }

        return {
            user,
            payments,
            application,
            owner,
            methods,
            isMobileView,
            receipt,
            payment_id,
            imageError,
            pay,
            openModal,
            closeModal,
            imageClick,
            imageChange
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
                                    ₱{{
                                        payment.partial != null
                                        ? parseFloat(payment.partial).toFixed(2)
                                        : payment.pending_payment != null
                                        ? parseFloat(payment.pending_payment).toFixed(2)
                                        : payment.amount_paid != null
                                        ? parseFloat(payment.amount_to_pay - payment.amount_paid).toFixed(2)
                                        : payment.amount_paid == null
                                        ? parseFloat(payment.amount_to_pay).toFixed(2)
                                        : ''
                                    }}
                                </p>
                            </div>

                            <div class="w-full flex justify-center items-center mb-2"
                                v-if="payment.amount_to_pay != payment.amount && !payment.pending_payment"
                            >
                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                    @click="pay(payment.id, 'GCash Payment')"
                                    v-if="methods.includes('GCash')"
                                >
                                    Pay Via GCash
                                </button>

                                <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                    @click="openModal(payment.id)"
                                    v-if="methods.includes('Bank')"
                                >
                                    Pay Via Bank
                                </button>
                            </div>

                            <div class="w-full" v-if="payment.pending_payment">
                                <p class="text-center font-bold mt-10"
                                    :style="{'font-size': isMobileView ? '10px': '12px'}"
                                >
                                    Wait for dorm owner to verify your payment.
                                </p>
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

            <div class="w-full">
                <div id="bankModal" class="bankModal mt-10 md:mt-0">
                    <div class="bank-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Upload Bank Transfer Receipt
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5">
                            <input type="file" id="bank_receipt" style="display: none"
                                @change="imageChange($event)"
                            />

                            <img :src="receipt ?? '/images/upload_image.png'"
                                class="cursor-pointer"
                                @click="imageClick()"
                                style="border: 1px solid black; border-radius: 5px; width: 100%; height: 260px;"
                            >

                            <p v-if="imageError" class="text-red-500 text-xs mt-1">
                                {{ imageError }}
                            </p>
                        </div>

                        <div class="w-full mt-5">
                            <button class="bg-cyan-500 py-2 px-5 rounded-md font-bold text-xs mr-1"
                                @click="pay(payment_id, 'Bank Payment')"
                            >
                                Upload
                            </button>
                        </div>
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

    .bankModal {
        display: none;
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 20px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .bank-modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 100%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    ::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* make scrollbar transparent */
    }
</style>
