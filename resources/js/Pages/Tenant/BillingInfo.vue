<script>
import TenantLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

export default {
    components: {
        VueDatePicker,
        TenantLayout,
    },
    setup() {
        const page = usePage();
        const user = page.props.auth.user;
        const options = ["Bank Transfer", "Online Payment"];

        const dorm = page.props.dorm;
        const room = page.props.room;
        const action = page.props.action
        const hasExistingApplication = page.props.hasExistingApplication
        const hasExistingReservation = page.props.hasExistingReservation

        onMounted(() => {
            const startDate = new Date();
            const endDate = new Date(
                new Date().setDate(startDate.getDate() + 6)
            );
            dateRange.value = [startDate, endDate];
        });

        const back = () => {
            var url = null;

            if (user) {
                router.get(route("view.dorm", room.dorm_id));
            } else {
                router.get(route("landing.page"));
            }
        };

        const showBankTransfer = ref(false);
        const toggleBankTransfer = () => {
            showBankTransfer.value = selectedPaymentMethod.value === 'Bank Transfer';
        };

        const allowedDates = [
            new Date(),
            new Date(new Date().setDate(new Date().getDate() + 1)),
            new Date(new Date().setDate(new Date().getDate() + 2)),
            new Date(new Date().setDate(new Date().getDate() + 3)),
            new Date(new Date().setDate(new Date().getDate() + 4)),
            new Date(new Date().setDate(new Date().getDate() + 5)),
            new Date(new Date().setDate(new Date().getDate() + 6)),
        ]

        const first_name = ref(null)
        const last_name = ref(null)
        const phone_number = ref(null)
        const date = ref();
        const dateRange = ref();
        const time = ref({
            hours: new Date().getHours(),
            minutes: new Date().getMinutes(),
        });
        const expired_at = ref(allowedDates[6])
        const amount_to_paid = ref(0)
        const selectedPaymentMethod = ref('');
        const proof_of_payment = ref(null);

        if(action == 'reserve') {
            amount_to_paid.value = 300;
        } else {
            amount_to_paid.value = parseInt(room.advance) + parseInt(room.deposit);
        }


        const proofPayment = () => {
            document.getElementById("proof_payment").click();
        };

        const proofPaymentChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                proof_of_payment.value = e.target.result;
            };
        };

        const move_in = ref()

        const submitApplication = () => {
            const request = {
                owner_id : dorm.user_id,
                tenant_id : user.id,
                dorm_id: dorm.id,
                room_id: room.id,
                status: 'rent',
                move_in: move_in.value
            }

            swal({
                title: `Are you sure to submit this application?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
            },
            function () {
                axios.post(route("application.apply"), request)
                    .then((response) => {
                        swal(
                            "Application submitted.",
                            `Wait for dorm owner approval.\n Note: Once its approved you will pay advance and deposit fee.`,
                            "success"
                        );

                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    })
                    .catch((error) => {
                        errors.value = error.response.data.errors;
                    });

            });
        }

        const reserveRoom = () => {
            const request = {
                owner_id : dorm.user_id,
                tenant_id : user.id,
                dorm_id: dorm.id,
                room_id: room.id,
                status: 'reserve',
                amount: amount_to_paid.value,
                check_date: date.value,
                check_time: time.value.hours + ':' + time.value.minutes,
                first_name: first_name.value,
                last_name: last_name.value,
                phone_number: phone_number.value,
                payment_method: selectedPaymentMethod.value,
                proof_of_payment: proof_of_payment.value
            }

            swal({
                title: `Are you sure to reserve this room?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
            },
            function () {
                axios.post(route("reserve.room"), request)
                    .then((response) => {
                        console.log(response.data.payment_method)
                        if(response.data.payment_method == "Online Payment") {
                            window.location.href = response.data.data.redirect.checkout_url
                        } else {
                            swal(
                                "Reservation submitted.",
                                `Wait for dorm owner approval.\n Note: If your reservation has decline you will be notify and you can refund your payment.`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        }
                    })
                    .catch((error) => {
                    });

            });
        }

        return {
            back,
            toggleBankTransfer,
            proofPaymentChange,
            proofPayment,
            selectedPaymentMethod,
            showBankTransfer,
            dorm,
            room,
            action,
            date,
            allowedDates,
            time,
            dateRange,
            options,
            amount_to_paid,
            first_name,
            last_name,
            phone_number,
            proof_of_payment,
            hasExistingApplication,
            submitApplication,
            reserveRoom,
            hasExistingReservation,
            move_in
        };
    },
};
</script>

<template>
    <TenantLayout>
        <div
            class="lg:pl-22 pt-14 min-h-screen w-screen transition-position lg:w-auto bg-gray-50"
        >
            <!--Di pa nagana back-->
            <section class="p-6 bg-gray-50 xl:max-w-7xl xl:mx-auto">
                <div class="w-full mb-3">
                    <p
                        class="cursor-pointer hover:text-orange-400"
                        @click="back()"
                    >
                        <i class="fa-solid fa-arrow-left md:fa-xl"></i>
                        <span class="font-bold ml-2"> Back </span>
                    </p>
                </div>
                <div class="flex items-center mb-6 justify-start">
                    <span
                        class="inline-flex justify-center items-center w-6 h-6 mr-2"
                        ><svg
                            viewBox="0 0 24 24"
                            width="20"
                            height="20"
                            class="inline-block"
                        >
                            <path
                                fill="currentColor"
                                d="M11,2V22C5.9,21.5 2,17.2 2,12C2,6.8 5.9,2.5 11,2M13,2V11H22C21.5,6.2 17.8,2.5 13,2M13,13V22C17.7,21.5 21.5,17.8 22,13H13Z"
                            ></path></svg></span>
                    <h1 class="text-4xl leading-tight">{{ action == 'reserve' ? 'Billing' : 'Apply'}}</h1>
                </div>
                <div
                    class="rounded-2xl flex-col shadow-lg p-5 bg-white flex mb-6"
                >
                    <div
                        class="col-span-2 flex mt-5 flex-col md:flex-row gap-8"
                    >
                        <!--Image ng Room-->
                        <div class="md:col-span-2">
                            <img
                                :src="room.image"
                                style="display: block; box-sizing: border-box"
                                class="rounded-xl shadow-[rgba(13,_38,_76,_0.19)_0px_9px_20px] w-[600px] h-[300px]"
                            />
                        </div>
                        <!--Room Details -->
                        <div class="flex-grow">
                            <div
                                className="bg-white rounded-xl w-full h-full px-5 border-neutral-200 overflow-hidden"
                            >
                                <div class="flex flex-row gap-5">
                                    <div>
                                        <div class="text-3xl mb-5">
                                            {{ room.name }}
                                        </div>
                                        <div class="font-semibold">
                                            Dorm:
                                            <span class="font-light">{{
                                                dorm.property_name
                                            }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Owner:
                                            <span class="font-light">{{
                                                dorm.user.name
                                            }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Deposit fee:
                                            <span class="font-light">{{
                                                room.deposit
                                            }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Advance fee:
                                            <span class="font-light">{{
                                                room.advance
                                            }}</span>
                                        </div>
                                        <div class="font-semibold">
                                            Monthy fee:
                                            <span class="font-light">{{
                                                room.fee
                                            }}</span>
                                        </div>
                                        <!--Capacity-->
                                        <div class="font-semibold">
                                            <div class="flex flex-row mt-1 gap-2">
                                                <i class="fa-solid fa-users"></i>
                                                <span class="font-light">{{
                                                    room.type_of_room
                                                }} person(s)</span>
                                            </div>

                                        </div>

                                        <div class="font-semibold">
                                            <div class="flex flex-row mt-1 gap-2">
                                    <svg
                                        height="20"
                                        width="20"
                                        version="1.1"
                                        id="Layer_1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                        viewBox="0 0 512 512"
                                        xml:space="preserve"
                                        fill="#000000"
                                    >
                                        <g
                                            id="SVGRepo_bgCarrier"
                                            stroke-width="0"
                                        ></g>
                                        <g
                                            id="SVGRepo_tracerCarrier"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        ></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g>
                                                <g>
                                                    <g>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M490.305,265.22H21.695C9.763,265.22,0,274.983,0,286.915v167.051 c0,11.932,9.763,21.695,21.695,21.695h33.627v14.102c0,11.932,9.763,21.695,21.695,21.695s21.695-8.678,21.695-21.695v-14.102 h314.576v14.102c0,11.932,9.763,21.695,21.695,21.695c10.847,0,20.61-8.678,21.695-21.695v-14.102h33.627 c11.932,0,20.61-9.763,21.695-21.695V286.915C512,274.983,502.237,265.22,490.305,265.22z M43.39,307.525h190.915v124.746H83.759 c-2.125-0.7-4.39-1.085-6.742-1.085s-4.617,0.384-6.742,1.085H43.39V307.525z M468.61,432.271h-26.885 c-2.125-0.7-4.39-1.085-6.742-1.085s-4.617,0.384-6.742,1.085H277.695V307.525H468.61V432.271z"
                                                        ></path>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M373.153,368.271h1.085c11.932,0,21.695-9.763,21.695-21.695s-9.763-21.695-21.695-21.695h-1.085 c-11.932,0-21.695,9.763-21.695,21.695S361.22,368.271,373.153,368.271z"
                                                        ></path>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M136.678,368.271h2.169c11.932,0,20.61-9.763,20.61-21.695s-9.763-21.695-21.695-21.695h-1.085 c-11.932,0-21.695,9.763-21.695,21.695S124.746,368.271,136.678,368.271z"
                                                        ></path>
                                                        <path
                                                            style="
                                                                fill: #010101;
                                                            "
                                                            d="M120.407,230.508h271.186c11.932,0,21.695-9.763,21.695-21.695V22.237 c0-11.932-9.763-21.695-21.695-21.695H120.407c-11.932,0-21.695,9.763-21.695,21.695v186.576 C98.712,220.746,108.475,230.508,120.407,230.508z M142.102,43.932h228.881v143.186H142.102V43.932z"
                                                        ></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                        <span class="font-light">{{
                                                room.furnished_type
                                            }}</span>
                                        </div>
                                         </div>

                                        <!--Aircon-->
                                        <div class="font-semibold">
                                            <div
                                                class="flex flex-row mt-1 gap-2"
                                            >
                                                <svg
                                                    width="20"
                                                    height="20"
                                                    stroke-width="1.5"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M22 3.6V11H2V3.6C2 3.26863 2.26863 3 2.6 3H21.4C21.7314 3 22 3.26863 22 3.6Z"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M18 7H19"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M2 11L2.78969 13.5844C3.04668 14.4255 3.82294 15 4.70239 15H6"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M22 11L21.2103 13.5844C20.9533 14.4255 20.1771 15 19.2976 15H18"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M9.5 14.5C9.5 14.5 9.5 21.5 6 21.5"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M14.5 14.5C14.5 14.5 14.5 21.5 18 21.5"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M12 14.5V21.5"
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>

                                                <span class="font-light">{{
                                                    room.is_aircon == "Yes"
                                                        ? "Arconditioned"
                                                        : "Not-Arconditioned"
                                                }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-5" v-if="(!!hasExistingApplication && !hasExistingApplication.move_in && action == 'rent') || (!hasExistingApplication && hasExistingReservation && hasExistingReservation.is_approved  && action == 'rent') || (!hasExistingApplication && !hasExistingReservation && action == 'rent')">
                                            <input type="date" v-model="move_in" class="rounded-md"/>
                                            <!-- {{ !!hasExistingApplication && !hasExistingApplication.move_in && action == 'rent' }}
                                            {{ !hasExistingApplication && hasExistingReservation && hasExistingReservation.is_approved  && action == 'rent' }}
                                            {{ !hasExistingApplication && !hasExistingReservation && action == 'rent' }} -->
                                        </div>
                                        <div class="mt-5" v-if="(!!hasExistingApplication && !hasExistingApplication.move_in && action == 'rent') || (!hasExistingApplication && hasExistingReservation && hasExistingReservation.is_approved  && action == 'rent') || (!hasExistingApplication && !hasExistingReservation && action == 'rent')">
                                            <button class="py-2 px-3 bg-cyan-400 rounded-md"
                                                @click="submitApplication()"
                                            >
                                                Submit Application
                                            </button>
                                        </div>
                                        <!-- <div v-if="!!hasExistingApplication && !hasExistingReservation">
                                            <span class="text-[8px] font-bold text-rose-500">
                                                You have pending application to this room, wait for dorm owner approval.
                                            </span>
                                        </div>

                                        <div v-if="!!hasExistingReservation && !hasExistingApplication">
                                            <span class="text-[8px] font-bold text-rose-500">
                                                You have pending reservation to this room, wait for dorm owner approval.
                                            </span>
                                        </div> -->

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="flex-grow">
                            <div className="bg-white rounded-xl w-full h-full px-5 border-neutral-200 overflow-hidden">

                            </div>
                        </div>
                    </div>
                    <hr class="h-px my-5 mx-5 bg-gray-200 border-0" />
                    <div class="flex-1 p-6" v-if="(!hasExistingApplication && !hasExistingReservation) && action == 'reserve'">
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            <div
                                class="rounded-lg border border-gray-300 shadow-sm"
                            >
                                <!--Header-->
                                <div
                                    class="text-2xl p-5 border-b border-gray-300 bg-gray-100"
                                >
                                    Billing Information
                                </div>
                                <!--Body-->
                                <form class="m-8">
                                    <div class="grid gap-6 mb-6">
                                        <div>
                                            <label
                                                for="first_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >First name</label
                                            >
                                            <input
                                                type="text"
                                                id="first_name"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="John"
                                                required
                                                v-model="first_name"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="last_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Last name</label
                                            >
                                            <input
                                                type="text"
                                                id="last_name"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Doe"
                                                required
                                                v-model="last_name"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="phone_number"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Phone number</label
                                            >
                                            <input
                                                type="tel"
                                                id="phone_number"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="123-45-678"
                                                pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}"
                                                required
                                                v-model="phone_number"
                                            />
                                        </div>
                                        <!--Matic malalagyan ng data-->
                                        <!-- <div>
                                            <label
                                                for="dorm_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Dorm Name</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="dorm_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                                v-model="dorm.property_name"
                                            />
                                        </div>

                                        <div>
                                            <label
                                                for="dorm_owner_name"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Dorm Owner Name</label
                                            >
                                            <input
                                                type="text"
                                                disabled
                                                id="dorm_name"
                                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Matic malalagyan ng data"
                                                required
                                                v-model="dorm.user.name"
                                            />
                                        </div> -->
                                        <!-- <div>
                                            <label
                                                for="date"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Reservation Date:
                                            </label>
                                            <VueDatePicker
                                                v-model="dateRange"
                                                range
                                                :enable-time-picker="false"
                                                disabled
                                            />
                                        </div> -->
                                        <!--7days dapat yung date simula from Jan 19, 1999-->

                                        <div v-if="action == 'reserve'">
                                            <label
                                                for="date"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >When will you check the
                                                reserved room?
                                            </label>
                                            <div
                                                class="flex flex-row items-center gap-2"
                                            >
                                                <div>
                                                    <VueDatePicker
                                                        v-model="date"
                                                        :allowed-dates="
                                                            allowedDates
                                                        "
                                                        :enable-time-picker="
                                                            false
                                                        "
                                                        placeholder="Select Date..."
                                                        required
                                                    />
                                                </div>
                                                <div class="flex-grow">
                                                    <VueDatePicker
                                                        v-model="time"
                                                        time-picker
                                                        :is-24="true"
                                                        :min-time="{
                                                            hours: 8,
                                                            minutes: 0,
                                                        }"
                                                        :max-time="{
                                                            hours: 17,
                                                            minutes: 30,
                                                        }"
                                                        placeholder="Select Time..."
                                                        required
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div
                                class="rounded-lg border border-gray-300 shadow-sm"
                            >
                                <div
                                    class="text-2xl p-5 border-b border-gray-300 bg-gray-100"
                                >
                                    Payment Information
                                </div>
                                    <div class="grid gap-6 m-8">
                                        <div>
                                            <!--300 sa reservation sa rent e advance + deposit-->
                                            <label
                                                for="amount"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Amount to be paid:</label
                                            >
                                            <input
                                                type="tel"
                                                id="amount"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                required
                                                disabled
                                                v-model="amount_to_paid"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="amount"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Payment Method</label
                                            >
                                            <select
                                                id="subject"
                                                v-model="selectedPaymentMethod"
                                                 @change="toggleBankTransfer"
                                                class="block w-full px-4 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-300 dark:border-gray-600 dark:placeholder-gray-400 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            >
                                                <option :value="null" disabled> Select Payment Method</option>
                                                <option
                                                    v-for="option in options"
                                                    :key="option"
                                                >
                                                    {{ option }}
                                                </option>
                                            </select>
                                        </div>
                                        <div id="bankpayment" v-if="showBankTransfer">
                                            <label
                                                for="amount"
                                                class="block mb-2 text-sm font-medium text-gray-900"
                                                >Bank Transfer</label
                                            >

                                            <div class="mb-4">

                                                <span
                                                    class="text-xs text-gray-500"
                                                    >Submit a proof of payment</span
                                                >
                                            </div>

                                            <input
                                                type="file"
                                                id="proof_payment"
                                                class="hidden"
                                                @change="proofPaymentChange($event)"
                                                accept="image/*"
                                            />

                                            <label
                                                for="proof_payment"
                                                class="relative cursor-pointer"
                                            >
                                                <div
                                                    class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                >
                                                    <img
                                                        v-if="!!proof_of_payment"
                                                        alt="Proof of Payment"
                                                        :src="proof_of_payment"
                                                        class="h-48 w-auto rounded-lg"
                                                    />
                                                    <span v-else
                                                        >Click to Input Image</span
                                                    >
                                                </div>
                                            </label>

                                            <InputError
                                                class="mt-2"

                                            />
                                        </div>


                                    </div>

                                    <button
                                        class="text-white float-right bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-3 mb-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mr-8"
                                        @click="reserveRoom()"
                                    >
                                        Proceed to Payment
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Billing Information-->
            </section>
        </div>
    </TenantLayout>
</template>
