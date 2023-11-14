<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { usePage, useForm, router } from '@inertiajs/vue3'
import { ref, reactive, watch, onMounted, computed } from 'vue';
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import 'mapbox-gl/dist/mapbox-gl.css';
import axios from 'axios';
import html2canvas from 'html2canvas';

export default{
    components:{
        AuthenticatedLayout,
    },
    setup(){
        const page = usePage();

        const reservation = page.props.reservation

        console.log(reservation)

        const formatDate = (d) => {
            const date = new Date(d); // Your date object

            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }

            return date.toLocaleDateString('en-US', options);
        }

        const formatTime = (t) => {
            // Creating a date object without specifying the date
            const date = new Date(`2000-01-01T${t}`);

            const options = { hour: 'numeric', minute: 'numeric', hour12: true };

            return date.toLocaleTimeString('en-US', options);
        }

        const getDaysRemaining = (reservation_date, expired_date) => {
            const date1 = new Date(reservation_date);
            const date2 = new Date(expired_date);

            // To calculate the time difference
            const timeDiff = Math.abs(date2.getTime() - date1.getTime());

            // Convert time difference to days
            return Math.ceil(timeDiff / (1000 * 3600 * 24));
        }

        const cancelReservation = () => {
            swal(
                {
                    title: `Are you sure to cancel this reservation?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    axios
                        .post(route("cancel.reservation"), {reservation_id: reservation.id})
                        .then((response) => {
                            swal(
                                "Reservation",
                                `You sucessfully cancel this reservation`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload()
                            }, 3000);
                        })
                        .catch((error) => {});
                }
            );
        }

        const move_in = ref();

        const rentNow = () => {
            swal(
                {
                    title: `Are you sure to rent this room?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    axios
                        .post(route("rent.now"), {
                            "owner_id" : reservation.owner,
                            "tenant_id" : reservation.tenant,
                            "dorm_id": reservation.dorm_id,
                            "room_id": reservation.room_id,
                            "move_in": move_in.value,
                            "reservation_id" : reservation.id
                        })
                        .then((response) => {
                            swal(
                                "Rent",
                                `You sucessfully rent this room.`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload()
                            }, 3000);
                        })
                        .catch((error) => {});
                }
            );
        }

        return {
            reservation,
            formatDate,
            formatTime,
            getDaysRemaining,
            cancelReservation,
            rentNow,
            move_in
        }
    }
}

</script>
<template>
    <AuthenticatedLayout>
        <div class="max-w-[2520px] mt-20 mx-auto xl:px-20 md:px-10 sm:px-2 px-4">
            <div
                className="
                        max-w-screen-lg
                        mx-auto
                        "
            ><div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" style="width: 40px; height: 40px;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1"><path d="M768 608a160 160 0 1 1-160 160 160 160 0 0 1 160-160z m0-32a192 192 0 1 0 192 192 192 192 0 0 0-192-192z" fill="#0070FF"/><path d="M864 768h-96v-64a16 16 0 0 0-32 0v80a16 16 0 0 0 16 16h112a16 16 0 0 0 0-32z" fill="#0070FF"/><path d="M933.12 502.4a24 24 0 0 1-33.92 0l-77.12-77.12v94.72a24 24 0 0 1-48 0v-142.72l-272-272-272 272V896a16 16 0 0 0 16 16h264a24 24 0 1 1 0 48h-264a64 64 0 0 1-64-64V425.28L104.96 502.4a24 24 0 0 1-33.92-33.92L456.8 82.72a64 64 0 0 1 90.56 0l385.76 385.76a24 24 0 0 1 0 33.92z" fill=""/><path d="M584 432v144h-144v-144z m0-32h-144a32 32 0 0 0-32 32v144a32 32 0 0 0 32 32h144a32 32 0 0 0 32-32v-144a32 32 0 0 0-32-32z" fill=""/></svg>
                    </span>
                    <h3 class="text-3xl font-bold">My Reservation</h3>
                </div>
                <hr class="h-px mt-5 bg-orange-400 border-1 dark:bg-gray-700" />
            <div class="mt-10" v-if="reservation">
                    <!--Dorm name-->
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold">{{ reservation.dorm.property_name }}</h3>
                    </div>
                    <p>{{ reservation.dorm.detailed_address }}</p>
                    <div
                        className="col-span-2 flex mt-5 flex-col md:flex-row gap-8"
                    >
                        <div>
                            <div class="md:col-span-3">
                                <img
                                    alt="dormimage"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                        height: 404px;
                                        width: 600px;
                                    "
                                    class="rounded-md shadow-md"
                                    width="882"
                                    height="404"
                                    :src="reservation.dorm.dorm_image"
                                />
                            </div>
                        </div>
                        <div class="flex-grow">
                            <div
                                className="bg-white rounded-xl border-[1px] w-full h-full shadow-lg p-4 border-neutral-200 overflow-hidden"
                            >
                                <h5 class="text-2xl font-bold">{{ reservation.room.name }}</h5>
                                <hr class="w-full my-2" />
                                <!--Room Image-->
                                <img
                                    alt="room1"
                                    style="
                                        display: block;
                                        box-sizing: border-box;
                                    "
                                    class="rounded-md shadow-md w-[350px] h-[200px]"
                                    width="882"
                                    height="404"
                                    :src="reservation.room.image"
                                />

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
                                    <p>{{ reservation.room.is_aircon == 'Yes' ? 'Aircondition' : 'Not Aircondition'}}</p>
                                </div>
                                <div class="flex flex-row mt-1 gap-2">
                                    <i class="fa-solid fa-users"></i>
                                    <p>{{reservation.room.type_of_room }}</p>
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
                                    <p>{{reservation.room.furnished_type }}</p>
                                </div>
                                <div>
                                    <p>When will you move in?</p>
                                    <input type="date" class="rounded-md mr-2" v-model="move_in"/>

                                    <button class="py-2 px-5 rounded-md bg-orange-400 text-white font-semibold mt-5"
                                        @click="rentNow()"
                                    >
                                        Rent Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full grid grid-cols-1 md:grid-cols-2 mt-5 gap-5 " v-if="reservation">
                    <div class="rounded-lg  border-[1px] bg-white shadow-lg p-5">
                        <p class="text-xl font-bold text-gray-900 mb-3">Reservation Details</p>
                        <div>
                            <p>Reservation Fee: ₱ 300</p>
                            <p>Visit Date: {{ formatDate(reservation.check_date) }}</p>
                            <p>Visit Time: {{ formatTime(reservation.check_time) }}</p>
                            <p>Reservation Date: {{ formatDate(reservation.created_at) }}</p>
                            <p>Reservation ends: {{ formatDate(reservation.expired_at) }}</p>
                            <p>Remaining: {{ getDaysRemaining(reservation.created_at, reservation.expired_at) }} day(s)</p>
                        </div>
                    </div>
                    <div class="rounded-lg  border-[1px] bg-white shadow-lg p-5">
                        <div class="flex flex-row items-center gap-3 mb-3">
                            <p class="text-xl font-bold text-gray-900 ">Cancellation Policy</p>
                            <p class="px-5 py-2 rounded-full text-xs text-gray-600 bg-gray-100">Non-refundable</p>
                        </div>

                        <div>
                            <p>If you cancel your reservation you will not get a refund</p>
                        </div>
                        <button class="py-2 px-5 rounded-full bg-red-500 text-white font-semibold mt-5"
                            @click="cancelReservation()"
                        >
                            Cancel Reservation
                        </button>
                    </div>

                </div>
                <div v-if="!reservation" class="p-5 h-40 flex w-full items-center justify-center text-lg bg-gradient-to-b from-gray-100 to-white">
                    You dont have reservation yet. Go to <a href="/" class="text-orange-400 underline mx-1"> Home Page </a>to make reservation
                </div>
            </div>

            

        </div>
    </AuthenticatedLayout>
</template>
