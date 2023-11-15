<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from 'vue'
import { usePage, router } from "@inertiajs/vue3";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import axios from "axios";

export default {
    components: {
        VueDatePicker,
        AuthenticatedLayout,
    },
    setup() {
        const headers = ["Subject", "Message", "Status"];
        const options = ["E-Wallet", "Cash", "Bank Transfer"];
        const openComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "block";
        };

        const closeComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "none";
        };
        const openLeaveModal = () => {
            var modal = document.getElementById("leaveModal");

            modal.style.display = "block";
        };

        const closeLeaveModal = () => {
            var modal = document.getElementById("leaveModal");

            modal.style.display = "none";
        };

        const showBankTransfer = ref(false);
        const showEwallet = ref(false);
        const toggleTransfer = () => {
            showBankTransfer.value = selectedPaymentMethod.value === 'Bank Transfer';
            showEwallet.value = selectedPaymentMethod.value === 'E-Wallet';
        };

        const page = usePage();

        const myDorm = ref()
        myDorm.value = page.props.myDorm

        console.log(myDorm.value)

        const currentRating = ref()
        currentRating.value = page.props.rating
        const rating = ref(currentRating.value ? currentRating.value.rate : 0)
        const comment = ref(currentRating.value ? currentRating.value.comment : null)


        const submitRatings = () => {

            swal({
                title: `Are you sure to rate this dorm?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
            },
            function () {
                axios.post(route("tenant.rate.dorm"),
                    {
                        rating: rating.value,
                        comment: comment.value,
                    }
                )
                    .then((response) => {
                        swal(
                            "You successfully rate this dorm.",
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

        const subject = ref()
        const complain = ref()

        const submitComplain = () => {
            swal({
                title: `Are you sure to submit this complain?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
            },
            function () {
                axios.post(route("tenant.submit.complain"),
                    {
                        subject: subject.value,
                        complain: complain.value,
                    }
                )
                    .then((response) => {
                        swal(
                            "You successfully submit complain.",
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

        const complaints = ref([])

        var complaintsArr = [];

        for(let c = 0; c < page.props.complaints.length; c++) {
            complaintsArr.push({
                subject: page.props.complaints[c].subject,
                complain: page.props.complaints[c].complain,
                status: page.props.complaints[c].status,
            })
        }

        complaints.value = complaintsArr;


        const date = ref(new Date());
        const reason = ref(null)
        const reason_description = ref(null)
        const selectedPaymentMethod = ref(null);
        const wallet_name = ref(null)
        const account_name = ref(null)
        const account_number = ref(null)

        const moveOut = () => {
            swal({
                title: `Are you sure to leave this dorm?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
            },
            function () {
                axios.post(route("tenant.move.out"),
                    {
                        move_out: date.value,
                        reason: reason.value,
                        reason_description: reason_description.value,
                        payment_method: selectedPaymentMethod.value,
                        wallet_name: wallet_name.value,
                        account_name: account_name.value,
                        account_number: account_number.value,
                    }
                )
                    .then((response) => {
                        swal(
                            "You successfully request to move out.",
                            "Please wait for dorm owner approval",
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

        const rateDorm = (r) => {
            rating.value = r
        }

        return {
            headers,
            date,
            reason,
            reason_description,
            wallet_name,
            account_name,
            account_number,
            complaints,
            options,
            openComplainModal,
            closeComplainModal,
            openLeaveModal,
            closeLeaveModal,
            selectedPaymentMethod,
            showBankTransfer,
            showEwallet,
            toggleTransfer,
            myDorm,
            submitRatings,
            currentRating,
            subject,
            complain,
            submitComplain,
            complaints,
            moveOut,
            rateDorm,
            rating,
            comment
        };
    },
};
</script>
<template>
    <AuthenticatedLayout>
        <div
            class="max-w-[2520px] mt-20 mx-auto xl:px-20 md:px-10 sm:px-2 px-4"
        >
            <div
                className="
                        max-w-screen-lg
                        mx-auto
                        "
            >
                <div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 0 384 512"
                        >
                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path
                                d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zM272 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"
                            />
                        </svg>
                    </span>
                    <h3 class="text-3xl font-bold">My Dorm</h3>
                </div>
                <hr class="h-px mt-5 bg-orange-400 border-1 dark:bg-gray-700" />
                <div class="mt-10" v-if="myDorm">
                    <!--Dorm name-->
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold">{{ myDorm.dorm.property_name }}</h3>
                        <button
                            class="py-1 px-4 text-white rounded-md bg-red-600"
                            @click="openLeaveModal()"
                            :disabled="myDorm.status == 'pending_move_out'"
                            :class="{'cursor-not-allowed': myDorm.status == 'pending_move_out'}"
                        >
                            Moved-out
                        </button>
                    </div>
                    <p>{{ myDorm.dorm.detailed_address }}</p>
                    <div
                        className="col-span-2 flex mt-5 flex-col md:flex-row gap-8"
                    >
                        <div>
                            <div class="md:col-span-3">
                                <img
                                    :src="myDorm.dorm.dorm_image"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                        height: 404px;
                                        width: 600px;
                                    "
                                    class="rounded-md shadow-md"
                                    width="882"
                                    height="404"
                                />
                            </div>
                        </div>
                        <div class="flex-grow">
                            <div
                                className="bg-white rounded-xl border-[1px] w-full h-full shadow-lg p-4 border-neutral-200 overflow-hidden"
                            >
                                <h5 class="text-2xl font-bold">Room 1</h5>
                                <hr class="w-full my-2" />
                                <!--Room Image-->
                                <img
                                    :src="myDorm.room.image"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                    "
                                    class="rounded-md shadow-md w-[350px] h-[200px]"
                                    width="882"
                                    height="404"
                                />
                                <!--Price ng room-->
                                <p class="mt-2">{{ myDorm.room.fee }}</p>
                                <!--Features ng room-->
                                <div class="flex flex-row mt-1 gap-2">
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
                                    <p>{{ myDorm.room.is_aircon == "Yes" ? "Airconditioned" : "Aircon not Available" }}</p>
                                </div>
                                <div class="flex flex-row mt-1 gap-2">
                                    <i class="fa-solid fa-users"></i>
                                    <p>{{ myDorm.room.type_of_room }}</p>
                                </div>
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
                                    <p>{{ myDorm.room.furnished_type }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr
                        class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700"
                    />
                    <!--Rate and review-->
                    <h3 class="text-2xl mt-4">Rate and Review</h3>
                    <div class="w-full mt-2">
                        <div style="cursor: pointer">
                            <i
                                data-alt="1"
                                class="fas fa-star active"
                                title=""
                                @click="rateDorm(1)"
                                :class="{'text-yellow-500': rating == 1 || rating > 1}"
                            ></i
                            >&nbsp;<i
                                data-alt="2"
                                class="fas fa-star active"
                                title=""
                                @click="rateDorm(2)"
                                :class="{'text-yellow-500': rating == 2 || rating > 2}"
                            ></i
                            >&nbsp;<i
                                data-alt="3"
                                class="fas fa-star active"
                                title=""
                                @click="rateDorm(3)"
                                :class="{'text-yellow-500': rating == 3 || rating > 3}"
                            ></i
                            >&nbsp;<i
                                data-alt="4"
                                class="fas fa-star active"
                                title=""
                                @click="rateDorm(4)"
                                :class="{'text-yellow-500': rating == 4 || rating > 4}"
                            ></i
                            >&nbsp;<i
                                data-alt="5"
                                class="fas fa-star active"
                                title=""
                                @click="rateDorm(5)"
                                :class="{'text-yellow-500': rating == 5}"
                            ></i
                            >
                            <!-- <input name="rating" type="hidden" value="5" /> -->
                        </div>
                        <textarea
                            id="message"
                            rows="4"
                            class="block p-2.5 w-full text-sm mt-2 text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 "
                            placeholder="Write your thoughts here..."
                            v-model="comment"
                        ></textarea>
                        <button
                            class="py-1 px-4 float-right mt-5 text-white rounded-md bg-orange-400 hover:bg-opacity-25"
                            @click="submitRatings()"
                        >
                            Submit
                        </button>
                    </div>
                    <hr
                        class="h-px mb-5 mt-16 bg-orange-400 border-1 dark:bg-gray-700"
                    />
                    <h3 class="text-2xl mt-4">Complaints</h3>
                    <div class="w-full mt-2 mb-5">
                        <div class="w-full mb-12 mt-5">
                            <app-card-table
                                ><div
                                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                                >
                                    <div
                                        class="rounded-t mb-0 px-4 py-3 border-0"
                                    >
                                        <div
                                            class="flex flex-wrap items-center"
                                        >
                                            <div
                                                class="relative w-full px-4 max-w-full flex-grow flex-1"
                                            >
                                                <button
                                                    class="py-2 px-4 text-white rounded-md bg-green-400 hover:bg-opacity-25"
                                                    @click="openComplainModal()"
                                                >
                                                    <span>
                                                        <i
                                                            class="fa-solid fa-plus mr-2"
                                                        ></i>
                                                    </span>
                                                    Make Complain
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="block w-full overflow-x-auto">
                                        <table
                                            class="items-center w-full bg-transparent border-collapse"
                                        >
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                                        v-for="header in headers"
                                                        :key="header"
                                                    >
                                                        {{ header }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        item, rowIndex
                                                    ) in complaints"
                                                    :key="rowIndex"
                                                >
                                                    <td
                                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                                        v-for="(
                                                            value, colIndex
                                                        ) in item"
                                                        :key="colIndex"
                                                    >
                                                        {{ value }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div></app-card-table
                            >
                        </div>
                    </div>
                </div>
                <div v-else class="p-5 h-40 flex w-full items-center justify-center text-lg bg-gradient-to-b from-gray-100 to-white">
                    You dont have leasing yet
                </div>
            </div>
            <!--Complain Modal-->
            <div
                    id="complainModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Make Complain
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeComplainModal()"
                                    >
                                        <svg
                                            class="w-3 h-3"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 14 14"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                            />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <form>
                                        <label for="subject" class="block mb-2 text-base font-medium text-black">Select Subject</label>
                                            <select id="subject" v-model="subject" class="block w-full px-4 py-3 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 ">
                                                <option selected>Choose a Subject</option>
                                                <option value="Maintenance">Maintenance Request</option>
                                                <option value="Cleanliness">Cleanliness</option>
                                                <option value="Noise">Noise and Disturbances</option>
                                                <option value="Safety and Security">Safety and Security</option>
                                                <option value="Bill Concerns">Bill Concerns</option>
                                                <option value="General Inquiries">General Inquiries</option>
                                                <option value="Others">Others</option>

                                            </select>
                                    <div class="w-full mb-4 border border-gray-200 mt-6 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                        <!-- <div class="flex items-center justify-between px-3 py-2 border-b dark:border-gray-600">
                                            <div class="flex flex-wrap items-center divide-gray-200 sm:divide-x dark:divide-gray-600">
                                                <div class="flex items-center space-x-1 sm:pr-4">
                                                    <button type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                                                <path d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z"/>
                                                                <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z"/>
                                                            </svg>
                                                        <span class="sr-only">Upload image</span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="px-4 py-2 bg-white rounded-b-lg dark:bg-gray-800">
                                            <label for="complainmessage" class="sr-only">Complain</label>
                                            <textarea id="complainmessage" v-model="complain" rows="8" class="block w-full px-0 text-sm text-gray-800 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="Write a Complain..." required></textarea>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                >
                                    <button
                                        @click.prevent="submitComplain()"
                                        type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Submit
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    id="leaveModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t "
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Moved-Out
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                        @click="closeLeaveModal()"
                                    >
                                        <svg
                                            class="w-3 h-3"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 14 14"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                            />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <div>
                                        <label for="movedoutdate" class="block mb-2 text-base font-medium text-black">Move-out Date:</label>
                                        <VueDatePicker
                                                v-model="date"
                                                :min-date="new Date()"
                                                :enable-time-picker="false"
                                            />
                                    </div>

                                    <form>
                                        <label for="subject" class="block mb-2 text-base font-medium text-black">Reason:</label>
                                            <select id="subject" v-model="reason" class="block w-full px-4 py-2 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 ">
                                                <option selected>Choose a Reason:</option>
                                                <option value="Relocation">Relocation</option>
                                                <option value="End of Lease">End of Lease</option>
                                                <option value="Housing Change">Housing Change</option>
                                                <option value="Financial Reasons">Financial Reasons</option>
                                                <option value="Personal Preference">Personal Preference</option>
                                                <option value="Family Circumstances">Family Circumstances</option>
                                                <option value="Others">Others</option>

                                            </select>
                                            <div class=" py-2 bg-white rounded-b-lg dark:bg-gray-800">
                                            <label for="complainmessage" class="sr-only">Complain</label>
                                            <textarea id="complainmessage" v-model="reason_description" rows="3" class="block w-full p-3 rounded-md text-sm text-gray-800 bg-gray-100 border-1 " placeholder="Write a Reason..." required></textarea>
                                            </div>
                                    </form>
                                    <div>
                                        <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deposit Refund Information:</p>
                                        <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room 101</p>
                                        <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deposit: P2,000.00</p>
                                        <form class="mt-4">
                                        <label for="subject" class="block mb-2 text-base font-medium text-black">Choose how to receive refund:</label>
                                            <select
                                                id="subject"
                                                v-model="selectedPaymentMethod"
                                                 @change="toggleTransfer"
                                                class="block w-full px-4 py-1 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 "
                                            >
                                                <option
                                                    v-for="option in options"
                                                    :key="option"
                                                >
                                                    {{ option }}
                                                </option>
                                            </select>
                                            <div class=" py-2 mt-2 bg-white rounded-b-lg" v-if="showEwallet">

                                                <form class="flex flex-col gap-1 ">
                                                    <div class="mb-3">
                                                        <label for="EWalletName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-wallet/Bank Name</label>
                                                        <input type="text" id="EWalletName" v-model="wallet_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Name:</label>
                                                        <input type="text" id="accName" v-model="account_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Number:</label>
                                                        <input type="text" id="accName" v-model="account_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                </form>
                                            </div>
                                            <div class=" py-2 mt-4 bg-white rounded-b-lg" v-if="showBankTransfer">
                                                <form class="flex flex-col gap-1">
                                                    <div class="mb-3">
                                                        <label for="BankName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-wallet/Bank Name</label>
                                                        <input type="text" id="BankName" v-model="wallet_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accbankName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Name:</label>
                                                        <input type="text" id="accbankName" v-model="account_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="accbankNumber" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Number:</label>
                                                        <input type="text" id="accbankNumber" v-model="account_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 ">
                                                    </div>
                                                </form>
                                            </div>
                                    </form>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="w-full border-t  border-gray-200"
                                >
                                    <button
                                        @click="moveOut()"
                                        type="button"
                                        class="text-white rounded-b-lg bg-red-600 hover:bg-opacity-25 font-medium w-full text-sm px-5 py-2.5"
                                    >
                                        "Move-Out & Deposit Refund Request"
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>
