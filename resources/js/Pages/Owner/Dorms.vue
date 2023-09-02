<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, reactive, watch } from 'vue';
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
        InputLabel,
        TextInput,
        MapboxMap,
        MapboxMarker
    },
    setup() {
        const page = usePage()

        const user = computed(() => page.props.auth.user);

        var timer = ref(undefined);
        const address = ref(null);
        const lat = ref(13.9382);
        const long = ref(120.72932);
        const detailed_address = ref(null);
        const description = ref(null);
        const amount = ref(0)
        const business_permit = ref(null)
        const image = ref(null)
        const rooms = ref([
            {
                name: null,
                description: null,
                image: null
            }
        ])

        const isMobileView = ref(false)

        isMobileView.value = screen.width < 600;

        const openFormModal = () => {
            var modal = document.getElementById("dormModal");

            modal.style.display = "block";
        }

        const closeFormModal = () => {
            var modal = document.getElementById("dormModal");

            modal.style.display = "none";
        }

        const getCoordinates = async (a) => {
            const url = `https://api.tomtom.com/search/2/geocode/${a}.json?key=wjvWAT9KJyQfZepSiABAgsa8idqpcLlG`

            await fetch(url)
                .then(response => response.json())
                .then(data => {
                    lat.value = parseFloat(data.results[0].position.lat);
                    long.value = parseFloat(data.results[0].position.lon);
                });
        }

        const mapDrag = async (e) => {
			lat.value = e.target._lngLat.lat
			long.value = e.target._lngLat.lng

            const coordinates = e.target._lngLat.lat + ',' + e.target._lngLat.lng

			const url = `https://api.tomtom.com/search/2/reverseGeocode/${coordinates}.json?key=wjvWAT9KJyQfZepSiABAgsa8idqpcLlG&radius=100`

            await fetch(url)
                .then(response => response.json())
                .then(data => {
                    address.value = data.addresses[0].address.municipalitySubdivision + ' ' + data.addresses[0].address.municipality
                });
        }

        const changeAddress = (e) => {
            clearTimeout(timer)

            timer = setTimeout(() => {
                if(address.value == '') {
                    getCoordinates('Waltermart Balayan')
                } else {
                    getCoordinates(e.target.value)
                }

            }, 2500)
        }

        const addRoom = () => {
            rooms.value.push(
                {
                    name: null,
                    description: null,
                    image: null
                }
            )
        }

        const saveDorm = () => {
            const data = new FormData();

            data.append("address", address.value);
            data.append("lat", lat.value);
            data.append("long", long.value);
            data.append("detailed_address", detailed_address.value);
            data.append("description", description.value);
            data.append("amount", amount.value);
            data.append("business_permit", business_permit.value);
            data.append("image", image.value);
            data.append("rooms", rooms.value);

            axios.post(route('save.dorm'), data)
                .then(response => {

                })
                .catch(error => {

                })
        }

        return {
            user,
            address,
            lat,
            long,
            detailed_address,
            description,
            amount,
            business_permit,
            image,
            rooms,
            isMobileView,
            openFormModal,
            closeFormModal,
            getCoordinates,
            mapDrag,
            changeAddress,
            addRoom,
            saveDorm
        };
    },
    mounted() {

    }
}

</script>

<template>
    <AuthenticatedLayout>
        <div class="main w-full">
            <div class="flex flex-col pr-5 pt-10">
                <div class="w-full">
                    <p class="float-right">
                        <i class="fa-solid fa-house-chimney-medical fa-xl cursor-pointer"
                            @click="openFormModal()"
                        ></i>
                    </p>
                </div>
            </div>

            <div id="dormModal" class="dormModal mt-10 md:mt-0">
                <div class="dorm-modal-content flex flex-col" :style="{width: isMobileView ? '95%' : '40%'}">
                    <div class="w-full">
                        <span class="text-lg font-bold">
                            New Dorm
                        </span>
                        <span class="float-right cursor-pointer"
                            @click="closeFormModal()"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                    </div>

                    <div class="w-full flex flex-col relative overflow-scroll"
                        :style="{height: isMobileView ? '450px' : '620px', border: '1px solid gray' }"
                    >
                        <div class="w-full p-2">
                            <div class="w-full">
                                <div>
                                    <InputLabel for="address" value="Dorm Address" />

                                    <TextInput
                                        id="address"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="address"
                                        required
                                        autofocus
                                        autocomplete="address"
                                        @input="changeAddress($event)"
                                    />
                                </div>
                            </div>

                            <div class="w-full mt-5 flex flex-row">
                                <div class="w-full mx-1">
                                    <MapboxMap
                                        style="height: 310px"
                                        access-token="pk.eyJ1IjoiYmFsb2dzeHh4IiwiYSI6ImNsbHA1dDN2MDAydGczZXFqZHprcW44dXIifQ.Z0dcyAB1W1B4-jcaqC_NKA"
                                        map-style="mapbox://styles/mapbox/streets-v11"
                                        :center="[long, lat]"
                                        :zoom="11"
                                    >
                                        <MapboxMarker :lng-lat="[long, lat]"  :draggable="true" @mb-dragend="mapDrag($event)"/>
                                    </MapboxMap>
                                </div>

                                <div class="w-full flex flex-col">
                                    <div class="w-full">
                                        <InputLabel for="detailed_address" value="Detailed Address" />

                                        <TextInput
                                            id="detailed_address"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="detailed_address"
                                            required
                                            autofocus
                                            autocomplete="detailed_address"
                                        />
                                    </div>

                                    <div class="w-full">
                                        <InputLabel for="description" value="Dorm Description" />

                                        <TextInput
                                            id="description"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="description"
                                            required
                                            autofocus
                                            autocomplete="description"
                                        />
                                    </div>

                                    <div class="w-full">
                                        <InputLabel for="amount" value="Dorm Price Per Month" />

                                        <TextInput
                                            id="amount"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="amount"
                                            required
                                            autofocus
                                            autocomplete="amount"
                                        />
                                    </div>

                                    <div class="w-full">
                                        <InputLabel for="business_permit" value="Business Permit" />

                                        <TextInput
                                            id="business_permit"
                                            type="file"
                                            class="mt-1 block w-full"
                                            v-model="business_permit"
                                            required
                                            autofocus
                                            autocomplete="business_permit"
                                        />
                                    </div>

                                    <div class="w-full">
                                        <InputLabel for="image" value="Dorm Image" />

                                        <TextInput
                                            id="image"
                                            type="file"
                                            class="mt-1 block w-full"
                                            v-model="image"
                                            required
                                            autofocus
                                            autocomplete="image"
                                        />
                                    </div>

                                </div>
                            </div>

                            <div class="w-full mt-5">
                                <button class="bg-cyan-500 text-center py-2 px-4 rounded-md float-right"
                                    @click="addRoom()"
                                >
                                    Add Room
                                </button>
                            </div>

                            <div class="flex flex-col w-full mt-2" v-for="(room, index) in rooms" :key="index">
                                <div class="w-full flex flex-row mx-1">
                                    <div class="w-full">
                                        <InputLabel for="room_name" value="Room Name" />

                                        <TextInput
                                            id="room_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="room.name"
                                            required
                                            autofocus
                                            autocomplete="room_name"
                                        />
                                    </div>

                                    <div class="w-full mx-1">
                                        <InputLabel for="room_image" value="Room Image" />

                                        <TextInput
                                            id="room_image"
                                            type="file"
                                            class="mt-1 block w-full"
                                            v-model="room.image"
                                            required
                                            autofocus
                                            autocomplete="room_image"
                                        />
                                    </div>

                                </div>

                                <div class="w-full">
                                    <InputLabel for="room_description" value="Room Description" />

                                    <TextInput
                                        id="room_description"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="room.desription"
                                        required
                                        autofocus
                                        autocomplete="room_description"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full mt-5">
                        <button class="w-full bg-cyan-500 text-center py-2 rounded-md"
                            @click="saveDorm()"
                        >
                            Save
                        </button>
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

    .dormModal {
        display: none; /* Hidden by default */
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
    .dorm-modal-content {
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
