<script>
import { ref, onMounted, reactive } from "vue";
import { router } from "@inertiajs/vue3";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide, Pagination, Navigation } from "vue3-carousel";
import { VueGoodTable } from 'vue-good-table-next';

export default {
    props: ["dorm", "user"],
    components: {
        VueGoodTable,
        MapboxMap,
        MapboxMarker,
        Carousel,
        Slide,
        Pagination,
        Navigation,
    },
    setup(props) {
        const isMobileView = ref(false);
        const room = ref(null);


        isMobileView.value = screen.width < 600;

        const openModal = () => {
            var modal = document.getElementById("roomModal");

            modal.style.display = "block";
        };
        const openReviewModal = () => {
            var modal = document.getElementById("rateReviewModal");

            modal.style.display = "block";
        };
        const closeReviewModal = () => {
            var modal = document.getElementById("rateReviewModal");

            modal.style.display = "none";
        };

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const closeModal = () => {
            var modal = document.getElementById("roomModal");

            modal.style.display = "none";
        };

        const viewRoom = (r) => {
            room.value = r;
            openModal();
        };

        const redirectToBillingInfo = (arg, action) => {
            const routeParam = arg.id + '-' + action;
            console.log(routeParam)

            router.get(route("tenant.billing_info", routeParam));
        }

        const reserveRoom = (arg) => {
            if (!props.user.income_information) {
                router.get(route("profile.edit"));

                return;
            }

            swal(
                {
                    title: `Are you sure to reserve this room?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    const data = {
                        dorm_id: props.dorm.id,
                        owner_id: props.dorm.user_id,
                        room_id: arg.id,
                        tenant_id: props.user.id,
                        status: "reserve",
                    };

                    axios
                        .post(route("reserve.room"), data)
                        .then((response) => {
                            swal(
                                "Application submitted.",
                                `Wait for dorm owner approval.\n Note: Once its approved you will pay ₱300`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        })
                        .catch((error) => {
                            errors.value = error.response.data.errors;
                        });
                }
            );
        };

        const rentRoom = (arg) => {
            if (!props.user.income_information) {
                router.get(route("profile.edit"));

                return;
            }

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
                    const data = {
                        dorm_id: props.dorm.id,
                        owner_id: props.dorm.user_id,
                        room_id: arg.id,
                        tenant_id: props.user.id,
                        status: "rent",
                    };

                    axios
                        .post(route("rent.room"), data)
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
                }
            );
        };

        const messageOwner = (owner_id) => {
            router.get(route("message.owner", owner_id));
        };

        return {
            props,
            isMobileView,
            room,
            moneyFormat,
            openModal,
            closeModal,
            viewRoom,
            reserveRoom,
            rentRoom,
            messageOwner,
            closeReviewModal,
            openReviewModal,
            redirectToBillingInfo
        };
    },
};
</script>

<template>

    <!-- IF LOGGED IN USER IS OWNER -->
    <!-- props.dorm.rooms para maccess yun mga room -->
    <div class="w-full" v-if="props.user && props.user.user_type == 'owner'">
        <!--Title-->
        <div class="flex items-center justify-start">
                <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                    <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block"><path fill="currentColor" d="M3,14L3.5,14.07L8.07,9.5C7.89,8.85 8.06,8.11 8.59,7.59C9.37,6.8 10.63,6.8 11.41,7.59C11.94,8.11 12.11,8.85 11.93,9.5L14.5,12.07L15,12C15.18,12 15.35,12 15.5,12.07L19.07,8.5C19,8.35 19,8.18 19,8A2,2 0 0,1 21,6A2,2 0 0,1 23,8A2,2 0 0,1 21,10C20.82,10 20.65,10 20.5,9.93L16.93,13.5C17,13.65 17,13.82 17,14A2,2 0 0,1 15,16A2,2 0 0,1 13,14L13.07,13.5L10.5,10.93C10.18,11 9.82,11 9.5,10.93L4.93,15.5L5,16A2,2 0 0,1 3,18A2,2 0 0,1 1,16A2,2 0 0,1 3,14Z"></path></svg>
                </span>
                <h3 class="text-3xl">Manage Room Availability</h3>
            </div>
            <div class="flex items-center justify-start mt-5">
                
                <h3 class="text-2xl">Dorm Name</h3>
            </div>
        <!--Overview-->
        <div
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            <!--Palagay na lang ng data pre-->
                        </p>
                        <p class="text-xs">TOTAL NO. OF Rooms</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                           <!--Palagay na lang ng data pre-->
                        </p>
                        <p class="text-xs">TOTAL NO. OF Available Rooms</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            <!--Palagay na lang ng data pre-->
                        </p>
                        <p class="text-xs">TOTAL NO. OF Unavailable Rooms</p>
                    </div>
                </div>
                
         </div>
         <!--Filtering Table-->
         <div class="my-10">
                <select
                class="w-40 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                <option value="Approved">Available</option>
                <option value="Declined">UnAvailable</option>
                </select>
            </div>
        <!--Table-->
        <div class="w-full pt-2 md:pt-20">
            <!--
                <vue-good-table
                    styleClass="vgt-table condensed"
                    style="padding: 0vw 5vw; width: 100%"
                    :columns="columns"
                    :pagination-options="{ enabled: true }"
                    :select-options="{ enabled: false }"
                    :search-options="{ enabled: true }"
                    >
                    <template>

                    </template>
                </vue-good-table>-->
        </div>
    </div>


    <div class="max-w-[2520px] xl:px-20 md:px-10 sm:px-2 px-4" v-if="!props.user || (!!props.user && props.user.user_type == 'tenant')">
                    <div
                        className="
                        max-w-screen-lg
                        mx-auto
                        "
                    >
    <div
        class="w-full flex flex-col justify-center items-center overflow-y-scroll"
    >


            <!--Header-->
            <div class="w-full px-5">
                <p class="w-full">
                    <span class="text-xl">
                        {{ props.dorm.detailed_address }}
                    </span>
                </p>

                <p class="text-md w-full"></p>

                <div class="w-full mt-5 flex justify-center items-center">
                    <img
                        :src="props.dorm.dorm_image"
                        class="w-full rounded-lg shadow-lg"
                        :style="{ height: isMobileView ? '200px' : '500px' }"
                    />
                </div>
            </div>
            <hr class="h-px my-5 bg-gray-200 border-0 dark:bg-gray-700" />
            <!--Room Section-->
            <div class="w-full px-5">
                <div class="w-full mb-8">
                    <p class="text-xl">Rooms</p>
                    <p class="text-sm w-full text-gray-400">(Select a room)</p>
                </div>
                <div>
                    <Carousel :items-to-show="1">
                        <Slide
                            v-for="(room, index) in props.dorm.rooms"
                            :key="index"
                        >
                            <!--Room Card-->
                            <div
                                class="w-64 lg:w-96 cursor-pointer bg-white rounded-lg"
                            >
                                <div>
                                    <img
                                        :src="room.image"
                                        class="rounded-lg w-full h-52 lg:h-96"
                                    />
                                </div>

                                <div class="pb-5 mt-2">
                                    <a>
                                        <p
                                            class="text-xl font-semibold tracking-tight text-gray-900"
                                        >
                                            {{ room.name }}
                                        </p>
                                    </a>
                                    <a>
                                        <p
                                            class="text-xs mx-12 bg-orange-500 p-3 w-auto rounded-md text-white"
                                        >
                                            {{
                                                room.is_available
                                                    ? "Available"
                                                    : "Not Available"
                                            }}
                                        </p>
                                    </a>
                                </div>

                                <button
                                    class="bg-cyan-500 items-center justify-center text-white text-sm py-4 w-full hover:bg-opacity-25 rounded-md"
                                    v-if="
                                        props.user &&
                                        props.user.user_type == 'tenant'
                                    "
                                    @click="viewRoom(room)"
                                >
                                    View
                                </button>
                            </div>
                        </Slide>

                        <template #addons>
                            <Navigation>
                                <template #next>
                                    <label
                                        for="carousel-2"
                                        class="inline-block text-orange-400 cursor-pointer translate-x-5 bg-white rounded-full shadow-md active:translate-y-0.5"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-10 w-10"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </label>
                                </template>
                                <template #prev>
                                    <label
                                        for="carousel-3"
                                        class="inline-block text-orange-400 cursor-pointer -translate-x-5 bg-white rounded-full shadow-md active:translate-y-0.5"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-10 w-10"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </label>
                                </template>
                            </Navigation>
                        </template>
                    </Carousel>
                </div>
            </div>

            <hr class="h-px my-5 bg-gray-200 border-0 dark:bg-gray-700" />

            <div
                className="
              w-full
              grid
              grid-cols-1
              md:grid-cols-7
              md:gap-10
            "
            >
                <div className="col-span-4 flex flex-col gap-8">
                    <div className="flex flex-col gap-2">
                        <div
                            className="
                            text-xl
                            lg:text-3xl
                            font-semibold
                            flex
                            flex-row
                            items-center
                            gap-2
                        "
                        >
                            <div>{{ props.dorm.property_name }}</div>
                        </div>
                        <div
                            className="
                        flex flex-row items-center gap-1 "
                        >
                            <div
                                className="text-md  text-neutral-600 font-semibold"
                            >

                            </div>
                            <div className="font-light text-neutral-600">
                                {{props.dorm.range_from }} - {{props.dorm.range_to }} a month
                            </div>
                        </div>
                        <div
                            className="
                            flex
                            flex-row
                            items-center
                            gap-4
                            font-light
                            text-sm
                            text-neutral-500
                        "
                        >
                            <div>{{ props.dorm.floors_total }} floor(s)</div>
                            <div>{{ props.dorm.rooms_total }} room(s)</div>
                        </div>
                    </div>
                    <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700" />

                    <!--Rules-->
                    <div>
                        <div
                            className="
                        text-xl
                            lg:text-3xl
                            font-semibold
                            flex
                            flex-row
                            items-center
                            gap-2
                        "
                        >
                            Rules
                        </div>

                        <div>
                            <p
                                class="mt-1"
                                v-if="props.dorm.rule.short_term === 'Yes'"
                            >
                                1.We accept short term minimum at
                                {{ props.dorm.rule.minimum_stay }} months
                            </p>
                            <p class="mt-1" v-else>
                                1. Short term is not allowed
                            </p>
                        </div>

                        <div>
                            <p
                                class="mt-1"
                                v-if="props.dorm.rule.mix_gender === 'Yes'"
                            >
                                2. Co-ed mixed gender is allowed
                            </p>
                            <p class="mt-1" v-else>
                                2. Co-ed mixed gender is not allowed
                            </p>
                        </div>

                        <div>
                            <p
                                class="mt-1"
                                v-if="props.dorm.rule.curfew === 'Yes'"
                            >
                                3. We have curfew hours at
                                {{ props.dorm.rule.curfew_hours }}
                            </p>
                            <p class="mt-1" v-else>
                                3. We do not have curfew hours.
                            </p>
                        </div>

                        <div class="mt-1">
                            <span
                                v-for="(r, index) in props.dorm.rule.rules"
                                :key="r"
                            >
                                <p>{{ index + 4 }}. {{ r }}</p>
                            </span>
                        </div>
                    </div>
                    <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700" />
                    <!--Amenities-->
                    <div class="w-full">
                        <p
                            class="text-xl lg:text-3xl font-semibold flex flex-row items-center gap-2"
                        >
                            Amenities
                        </p>

                        <div class="mt-2">
                            <span v-for="a in props.dorm.amenities" :key="a">
                                <div
                                    class="inline-block bg-orange-600 text-white rounded p-2 m-1"
                                >
                                    {{ a.amenity }}
                                </div>
                            </span>
                        </div>
                    </div>
                    <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700" />
                    <!--MapBox-->
                    <div class="w-full">
                        <p
                            class="text-xl lg:text-3xl font-semibold flex flex-row mb-5 items-center gap-2"
                        >
                            Location
                        </p>

                        <MapboxMap
                            class="rounded-lg shadow-md lg:mb-8"
                            :style="{
                                height: isMobileView ? '300px' : '425px',
                            }"
                            access-token="pk.eyJ1IjoiYmFsb2dzeHh4IiwiYSI6ImNsbHA1dDN2MDAydGczZXFqZHprcW44dXIifQ.Z0dcyAB1W1B4-jcaqC_NKA"
                            map-style="mapbox://styles/mapbox/streets-v11"
                            :center="[props.dorm.long, props.dorm.lat]"
                            :zoom="15"
                        >
                            <MapboxMarker
                                :lng-lat="[props.dorm.long, props.dorm.lat]"
                            />
                        </MapboxMap>
                    </div>
                </div>

                <!--Owner Information-->
                <div
                    className="order-last my-10 sm:my-0 md:order-last sm:col-span-3"
                >

                    <div
                        class="flex items-center cursor-pointer hover:text-orange-400 justify-center mb-8"
                        v-if="props.user && props.user.user_type == 'tenant'"
                    >
                        <a
                            @click="openReviewModal()"
                        >
                            <i class="hp-icon fas fa-star"></i>
                            <span class="text-xl">Write a Review</span>
                        </a>
                    </div>

                    <div
                        className="bg-white rounded-xl border-[1px] shadow-lg p-12 border-neutral-200 overflow-hidden"
                    >
                        <!-- Common elements for all screen sizes -->

                        <div
                            className="flex flex-row items-center justify-center gap-1  "
                        >
                            <div class="mr-4">
                                <img
                                    :src="
                                        props.dorm.user.image ??
                                        'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'
                                    "
                                    alt="Profile picture"
                                    class="rounded-full w-32 h-32 bg-gray-100 dark:bg-slate-800"
                                />
                            </div>
                        </div>

                        <div
                            className="flex flex-row items-center justify-center gap-1 pt-4 md:pt-4 pb-3"
                        >
                            <p class="text-xl">
                                Hosted by:
                                <span class="font-bold"
                                    >{{ props.dorm.user.first_name }}
                                    {{ props.dorm.user.last_name }}</span
                                >
                            </p>
                        </div>

                        <div
                            className="flex flex-row items-center justify-center gap-1 md:mx-5"
                        >
                            <p class="text-sm text-slate-500" value="">
                                MEMBER SINCE 2018-19-02
                            </p>
                        </div>

                        <div
                            className="flex flex-row items-center justify-center gap-1 md:mx-5"
                        >
                            <p class="text-sm text-slate-500" value="">
                                {{ props.dorm.user.bio }}
                            </p>
                        </div>

                        <!-- Conditional layout for sm screens (horizontal) -->

                        <footer class="items-center justify-center flex mt-3">
                            <button
                                class="text-white float-left bg-orange-400 hover:bg-orange-200 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm p-4"
                                @click="messageOwner(props.dorm.user_id)"
                                v-if="props.user"
                            >
                                Message
                            </button>
                        </footer>
                    </div>
                </div>
            </div>
            
            <!--rateReviewModal-->
            <div
                id="rateReviewModal"
                tabindex="-1"
                aria-hidden="true"
                style="background-color: rgba(0, 0, 0, 0.7)"
                class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            >
                <div class="h-screen flex justify-center items-center">
                    <div class="relative w-full max-w-xl max-h-full">
                        <!-- Modal content -->
                        <div
                            class="relativerounded-lg shadow rounded-xl bg-white"
                        >
                            <!-- Modal header -->
                            <div
                                class="flex items-start justify-between p-4 rounded-t dark:border-gray-600"
                            >
                                <button
                                    type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    @click="closeReviewModal()"
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
                            <div class="p-10 flex flex-col gap-5">
                                <h3 class="text-2xl font-semibold text-black">
                                    Write a review
                                </h3>
                                <p class="text-lg font-light text-gray-600">
                                    Rating
                                </p>
                                <div style="cursor: pointer">
                                    <i
                                        data-alt="1"
                                        class="fas fa-star active"
                                        title=""
                                    ></i
                                    >&nbsp;<i
                                        data-alt="2"
                                        class="fas fa-star active"
                                        title=""
                                    ></i
                                    >&nbsp;<i
                                        data-alt="3"
                                        class="fas fa-star active"
                                        title=""
                                    ></i
                                    >&nbsp;<i
                                        data-alt="4"
                                        class="fas fa-star active"
                                        title=""
                                    ></i
                                    >&nbsp;<i
                                        data-alt="5"
                                        class="fas fa-star active"
                                        title=""
                                    ></i
                                    ><input
                                        name="rating"
                                        type="hidden"
                                        value="5"
                                    />
                                </div>
                                <label
                                    for="message"
                                    class="block mb-2 text-lg font-medium text-gray-900 dark:text-white"
                                    >Review</label
                                >
                                <textarea
                                    id="message"
                                    rows="4"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Write your thoughts here..."
                                ></textarea>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                            >
                                <button
                                    @click="closeTutModal()"
                                    type="button"
                                    class="text-white w-full rounded-2xl bg-orange-400 hover:bg-orange-300 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center"
                                >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Room modal-->
            <div class="w-full">
                <div id="roomModal" class="roomModal mt-10 md:mt-0">
                    <div
                        class="room-modal-content flex flex-col"
                        :style="{ width: isMobileView ? '97%' : '30%' }"
                        v-if="room"
                    >
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                {{ room.name }}
                            </span>
                            <span
                                class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5">
                            <img :src="room.image" alt="" class="room-image" />
                        </div>

                        <div class="w-full mt-8 flex flex-row">
                            <div class="w-full text-center">
                                <p class="text-sm font-bold">Deposit Fee</p>

                                <p class="text-xs">
                                    {{ moneyFormat(room.deposit) }}
                                </p>
                            </div>

                            <div class="w-full text-center">
                                <p class="text-sm font-bold">Advance Fee</p>

                                <p class="text-xs">
                                    {{ moneyFormat(room.advance) }}
                                </p>
                            </div>

                            <div class="w-full text-center">
                                <p class="text-sm font-bold">Monthly Fee</p>

                                <p class="text-xs">
                                    {{ moneyFormat(room.fee) }}
                                </p>
                            </div>
                        </div>

                        <div class="w-full mt-5 flex flex-row">
                            <div class="w-full text-center">
                                <p class="text-sm font-bold">Capacity</p>

                                <p class="text-xs">
                                    {{ room.type_of_room }}
                                </p>
                            </div>

                            <div class="w-full text-center">
                                <p class="text-sm font-bold">Furnished Type</p>

                                <p class="text-xs">
                                    {{ room.furnished_type }}
                                </p>
                            </div>

                            <div class="w-full text-center">
                                <p class="text-sm font-bold">
                                    Is Airconditioned?
                                </p>

                                <p class="text-xs">
                                    {{ room.is_aircon }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="w-full flex justify-center items-center mt-10"
                        >
                            <button
                                class="text-md bg-orange-500 mx-2 text-white p-5 rounded-md"
                                @click="redirectToBillingInfo(room, 'reserve')"
                                :class="{
                                    'cursor-not-allowed': !room.is_available,
                                }"
                                :disabled="!room.is_available"
                                v-if="user.is_approved"
                            >
                                Reserve
                            </button>

                            <button
                                class="text-md bg-cyan-500 text-white    mx-2 p-5 rounded-md"
                                @click="redirectToBillingInfo(room, 'rent')"
                                :class="{
                                    'cursor-not-allowed': !room.is_available,
                                }"
                                :disabled="!room.is_available"
                                v-if="user.is_approved"
                            >
                                Rent
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</div>
</template>

<style>
.room-image {
    width: 100%;
    height: 200px;
    border-radius: 5px;
    border: 1px solid gray;
}

.generic-image {
    width: 100%;
    height: 300px;
    border-radius: 5px;
    border: 1px solid black;
}

hr {
    border: 0;
    clear: both;
    display: block;
    width: 100%;
    background-color: black;
    height: 1px;
}

.roomModal {
    display: none;
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0, 0, 0); /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
}

/* Modal Content */
.room-modal-content {
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
