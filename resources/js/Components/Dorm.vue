<script>
import { ref, onMounted, reactive } from 'vue';
import { router } from '@inertiajs/vue3'
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

export default {
    props: ['dorm', 'user'],
    components: {
        MapboxMap,
        MapboxMarker,
        Carousel,
        Slide,
        Pagination,
        Navigation
    },
    setup(props){

        const isMobileView = ref(false)
        const room = ref(null);

        isMobileView.value = screen.width < 600;

        const openModal = () => {

            var modal = document.getElementById("roomModal");

            modal.style.display = "block";

        }

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2)

            return '₱ ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');;
        }

        const closeModal = () => {
            var modal = document.getElementById("roomModal");

            modal.style.display = "none";
        }

        const viewRoom = (r) => {
            room.value = r
            openModal()
        }

        return {
            props,
            isMobileView,
            room,
            moneyFormat,
            openModal,
            closeModal,
            viewRoom
        }
    }
}
</script>

<template>
    <div class="w-full flex flex-col justify-center items-center">
        <div :style="{width : isMobileView ? '100%' : '60%'}">
            <div class="w-full px-5">
                <p class="w-full">
                    <span class="text-xl">
                        {{ props.dorm.property_name }}
                    </span>

                    <span class="bg-orange-500 px-1 py-1 float-right rounded-md text-xs cursor-pointer">
                        Message
                    </span>
                </p>

                <p class="text-md w-full">
                    {{ props.dorm.detailed_address }}
                </p>

                <div class="w-full mt-5 flex justify-center items-center">
                    <img :src="props.dorm.dorm_image" class="w-full"
                        :style=" {height: isMobileView ? '200px' : '500px' }"
                    />
                </div>
            </div>

            <div class="w-full px-5 pt-2 md:pt-5">
                <div class="w-full">
                    <p class="text-sm">
                        Rooms
                    </p>
                </div>

                <Carousel :items-to-show="isMobileView ? 1 : props.dorm.rooms.length > 3 ? 3 : props.dorm.rooms.length"
                    :wrap-around="true"
                >
                    <Slide v-for="(room, index) in props.dorm.rooms" :key="index">
                        <div class="flex flex-col w-full md:px-1">
                            <div class="w-full">
                                <img :src="room.image"
                                    class="room-image"
                                >
                            </div>

                            <div class="w-full">
                                <p class="text-lg">
                                    {{  room.name }}
                                </p>
                            </div>

                            <div class="w-full flex justify-center items-center">
                                <button class="bg-cyan-500 px-2 py-1 text-xs rounded-md"
                                    @click="viewRoom(room)"
                                >
                                    View
                                </button>
                            </div>
                        </div>
                    </Slide>

                    <template #addons>
                        <!-- <Navigation /> -->
                        <Pagination />
                    </template>
                </Carousel>
            </div>

            <div class="w-full px-5 mt-5">
                <hr/>
            </div>

            <div class="w-full px-5 my-5 flex flex-col md:flex-row">
                <div class="w-full flex flex-col mr-2">
                    <div class="w-full">
                        <p class="text-lg">
                            {{ props.dorm.user.first_name }} {{ props.dorm.user.last_name }}
                        </p>

                        <p class="text-xs">
                            {{ props.dorm.floors_total }} Floor(s) - {{ props.dorm.rooms_total }} Room(s)
                        </p>
                    </div>

                    <div class="w-full mt-3">
                        <hr/>
                    </div>

                    <div class="w-full mt-3">
                        <div class="w-full">
                            <p class="text-sm font-bold">
                                Rules
                            </p>

                            <p class="text-xs mt-2 font-bold">
                                Is short-term allowed?
                            </p>

                            <p class="text-xs mt-1">
                                {{ props.dorm.rule.short_term }}
                            </p>

                            <p class="text-xs mt-2 font-bold">
                                Is Co-ed mixed gender allowed?
                            </p>

                            <p class="text-xs mt-1">
                                {{ props.dorm.rule.mix_gender }}
                            </p>

                            <p class="text-xs mt-2 font-bold">
                                Do you have a curfew?
                            </p>

                            <p class="text-xs mt-1">
                                {{ props.dorm.rule.curfew }}
                            </p>

                            <p class="text-xs mt-2 font-bold">
                                What is the curfew hours?
                            </p>

                            <p class="text-xs mt-1">
                                {{ props.dorm.rule.curfew_hours }}
                            </p>

                            <p class="text-xs mt-2 font-bold">
                                What is the minimum stayed allowed?
                            </p>

                            <p class="text-xs mt-1">
                                {{ props.dorm.rule.minimum_stay }}
                            </p>

                            <p class="text-xs mt-2 font-bold">
                                Custom Rule(s)
                            </p>

                            <p class="text-xs mt-1">
                                <!-- {{ props.dorm.rule.rules.toString() }} -->
                                <span v-for="r in props.dorm.rule.rules" :key="r">
                                    {{ r }}{{ r == props.dorm.rule.rules[props.dorm.rule.rules.length - 1] ? '' : ', ' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="w-full mt-3">
                        <hr/>
                    </div>

                    <div class="w-full mt-3">
                        <p class="text-sm font-bold">
                            Amenities
                        </p>

                        <p class="text-xs mt-2">
                            <span v-for="a in props.dorm.amenities" :key="a">
                                {{ a.amenity }}{{ a.amenity == props.dorm.amenities[props.dorm.amenities.length - 1]['amenity'] ? '' : ', ' }}
                            </span>
                        </p>
                    </div>

                    <div class="w-full my-3" v-if="isMobileView">
                        <hr/>
                    </div>
                </div>

                <div class="w-full mt-5 md:mt-0">
                    <MapboxMap
                        :style="{height: isMobileView ? '300px' : '425px'}"
                        style="border: 1px solid black"
                        access-token="pk.eyJ1IjoiYmFsb2dzeHh4IiwiYSI6ImNsbHA1dDN2MDAydGczZXFqZHprcW44dXIifQ.Z0dcyAB1W1B4-jcaqC_NKA"
                        map-style="mapbox://styles/mapbox/streets-v11"
                        :center="[props.dorm.long, props.dorm.lat]"
                        :zoom="15"
                    >
                        <MapboxMarker :lng-lat="[props.dorm.long, props.dorm.lat]"/>
                    </MapboxMap>
                </div>
            </div>
        </div>

        <div class="w-full">
            <div id="roomModal" class="roomModal mt-10 md:mt-0">
                <div class="room-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}" v-if="room">
                    <div class="w-full">
                        <span class="text-lg font-bold">
                            {{ room.name}}
                        </span>
                        <span class="float-right cursor-pointer"
                            @click="closeModal()"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                    </div>

                    <div class="w-full mt-5">
                        <img :src="room.image" alt="" class="room-image">
                    </div>

                    <div class="w-full mt-8 flex flex-row">
                        <div class="w-full text-center">
                            <p class="text-sm font-bold">
                                Deposit Fee
                            </p>

                            <p class="text-xs">
                                {{ moneyFormat(room.deposit) }}
                            </p>
                        </div>

                        <div class="w-full text-center">
                            <p class="text-sm font-bold">
                                Advance Fee
                            </p>

                            <p class="text-xs">
                                {{ moneyFormat(room.advance) }}
                            </p>
                        </div>

                        <div class="w-full text-center">
                            <p class="text-sm font-bold">
                                Monthly Fee
                            </p>

                            <p class="text-xs">
                                {{ moneyFormat(room.fee) }}
                            </p>
                        </div>
                    </div>

                    <div class="w-full mt-5 flex flex-row">
                        <div class="w-full text-center">
                            <p class="text-sm font-bold">
                                Type of Room
                            </p>

                            <p class="text-xs">
                                {{ room.type_of_room }}
                            </p>
                        </div>

                        <div class="w-full text-center">
                            <p class="text-sm font-bold">
                                Furnished Type
                            </p>

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

                    <div class="w-full flex justify-center items-center mt-10">
                        <button class="text-md bg-orange-500 mx-2 px-2 py-1 rounded-md">
                            Reserve
                        </button>

                        <button class="text-md bg-cyan-500 mx-2 px-2 py-1 rounded-md">
                            Rent
                        </button>
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
  clear:both;
  display:block;
  width: 100%;
  background-color: black;
  height: 1px;
}

.roomModal {
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
