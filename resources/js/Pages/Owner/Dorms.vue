<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
// import AuthenticatedLayout from '@/Layouts/ResponsiveLayout.vue';
import { usePage, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, reactive, watch, onMounted, computed } from "vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";
import LvProgressBar from "lightvue/progress-bar";
import { router } from "@inertiajs/vue3";
import DormList from "@/Components/DormList.vue";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

export default {
    components: {
        AuthenticatedLayout,
        InputLabel,
        TextInput,
        DormList,
        MapboxMap,
        MapboxMarker,
        LvProgressBar: LvProgressBar,
        router,
        PulseLoader,
    },
    setup() {
        const page = usePage();

        const user = computed(() => page.props.auth.user);

        const data = new FormData();
        const errors = ref(null);
        const active = ref(0);
        const progress = ref(20);
        var timer = ref(undefined);
        const id = ref(null);
        const address = ref("");
        const lat = ref(13.9382);
        const long = ref(120.72932);
        const detailed_address = ref("");
        const property_name = ref("");
        const description = ref("");
        const floors_total = ref("");
        const rooms_total = ref("");
        const rooms = ref([]);
        const amenities = ref([]);
        const short_term = ref("No");
        const mix_gender = ref("No");
        const curfew = ref("No");
        const curfew_hours = ref("");
        const minimum_stay = ref("");
        const rules = ref([]);
        const dorm_image = ref("");
        const dorm_image_src = ref(null);
        const business_permit_image = ref("");
        const business_permit_image_src = ref(null);
        const termsAndCondition = ref([]);

        const isMobileView = ref(false);

        isMobileView.value = screen.width < 600;

        const openFormModal = () => {
            var modal = document.getElementById("dormModal");

            modal.style.display = "flex";

            data.value = new FormData();
        };

        const closeFormModal = () => {
            var modal = document.getElementById("dormModal");

            modal.style.display = "none";
        };

        const getCoordinates = async (a) => {
            const url = `https://api.tomtom.com/search/2/geocode/${a}.json?key=wjvWAT9KJyQfZepSiABAgsa8idqpcLlG`;

            await fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    lat.value = parseFloat(data.results[0].position.lat);
                    long.value = parseFloat(data.results[0].position.lon);
                });
        };

        const mapDrag = async (e) => {
            lat.value = e.target._lngLat.lat;
            long.value = e.target._lngLat.lng;

            const coordinates =
                e.target._lngLat.lat + "," + e.target._lngLat.lng;

            const url = `https://api.tomtom.com/search/2/reverseGeocode/${coordinates}.json?key=wjvWAT9KJyQfZepSiABAgsa8idqpcLlG&radius=100`;

            await fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    address.value =
                        data.addresses[0].address.municipalitySubdivision +
                        " " +
                        data.addresses[0].address.municipality;
                });
        };

        const changeAddress = (e) => {
            clearTimeout(timer);

            timer = setTimeout(() => {
                if (address.value == "") {
                    getCoordinates("Waltermart Balayan");
                } else {
                    getCoordinates(e.target.value);
                }
            }, 2500);
        };

        const roomImageClick = (arg) => {
            document.getElementById(arg).click();
        };

        const roomImageChange = (e, arg, index) => {
            const image = e.target.files[0];

            rooms.value[index].image = image;

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                rooms.value[index].src = e.target.result;
            };
        };

        const dormImageClick = () => {
            document.getElementById("dorm_image").click();
        };

        const dormImageChange = (e) => {
            const image = e.target.files[0];

            dorm_image.value = image;

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                dorm_image_src.value = e.target.result;
            };
        };

        const bpImageClick = () => {
            document.getElementById("business_permit").click();
        };

        const bpImageChange = (e) => {
            const image = e.target.files[0];

            business_permit_image.value = image;

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                business_permit_image_src.value = e.target.result;
            };
        };

        const addRoom = () => {
            rooms.value.push({
                id: null,
                name: null,
                type_of_room: null,
                is_aircon: null,
                furnished_type: null,
                image: null,
                src: null,
                fee: 0,
                advance: 0,
                deposit: 0,
                is_available: null,
            });
        };

        const removeRoom = (index) => {
            rooms.value = rooms.value.filter((x, i) => {
                return i != index;
            });
        };

        const addRule = () => {
            rules.value.push({
                name: null,
            });
        };

        const removeRule = (index) => {
            rules.value = rules.value.filter((x, i) => {
                return i != index;
            });
        };

        const addAmenities = (amenity) => {
            if (amenities.value.includes(amenity)) {
                amenities.value = amenities.value.filter((x) => {
                    return x != amenity;
                });
            } else {
                amenities.value.push(amenity);
            }
        };

        const validationError = (field, errors) => {
            if (errors) {
                if (errors.hasOwnProperty(field)) {
                    return Array.isArray(errors[field])
                        ? errors[field][0]
                        : errors[field];
                }
            }

            return null;
        };

        const editDorm = (dorm) => {
            active.value = 1;
            id.value = dorm.id;
            address.value = dorm.map_address;
            lat.value = dorm.lat;
            long.value = dorm.long;
            detailed_address.value = dorm.detailed_address;
            property_name.value = dorm.property_name;
            description.value = dorm.description;
            floors_total.value = dorm.floors_total;
            rooms_total.value = dorm.rooms_total;
            rooms.value = dorm.rooms;
            amenities.value = dorm.amenities.map((x) => {
                return x.amenity;
            });
            short_term.value = dorm.rule.short_term;
            mix_gender.value = dorm.rule.mix_gender;
            curfew.value = dorm.rule.curfew;
            curfew_hours.value = dorm.rule.curfew_hours;
            minimum_stay.value = dorm.rule.minimum_stay;
            rules.value = dorm.rule.rules.map((x) => {
                return {
                    name: x,
                };
            });
            dorm_image.value = dorm.dorm_image;
            business_permit_image.value = dorm.business_permit_image;
            dorm_image_src.value = dorm.dorm_image;
            business_permit_image_src.value = dorm.business_permit_image;

            openFormModal();
        };

        const loading = ref(false);

        const saveDorm = () => {
            swal(
                {
                    title: "Are you sure to save this dorm?",
                    type: "success",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true,
                },
                function () {
                    // swal("Deleted!", "Your imaginary file has been deleted.", "success");
                    loading.value = true;

                    if (!!id.value) {
                        data.append("id", id.value);
                    }

                    // Dorms Table
                    data.append("map_address", address.value);
                    data.append("lat", lat.value);
                    data.append("long", long.value);
                    data.append("detailed_address", detailed_address.value);
                    data.append("property_name", property_name.value);
                    data.append("description", description.value);
                    data.append("floors_total", floors_total.value);
                    data.append("rooms_total", rooms_total.value);
                    data.append("dorm_image", dorm_image.value);
                    data.append(
                        "business_permit_image",
                        business_permit_image.value
                    );

                    // Rooms Table
                    data.append("rooms", JSON.stringify(rooms.value));

                    // Amenities Table
                    data.append("amenities", JSON.stringify(amenities.value));

                    // Rules Table
                    data.append("short_term", short_term.value);
                    data.append("mix_gender", mix_gender.value);
                    data.append("curfew", curfew.value);
                    data.append("curfew_hours", curfew_hours.value);
                    data.append("minimum_stay", minimum_stay.value);
                    data.append("rules", JSON.stringify(rules.value));


                    // data.append("range_from", range_from.value);
                    // data.append("range_to", range_to.value);



                    axios
                        .post(route("save.dorm"), data)
                        .then((response) => {
                            loading.value = false;

                            swal(
                                "Success!",
                                "Your dorm has been save.",
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        })
                        .catch((error) => {
                            errors.value = error.response.data.errors;
                            loading.value = false;
                        });
                }
            );
        };

        const dorms = ref([]);

        onMounted(() => {
            dorms.value = page.props.dorms;
        });
        const selectedStatus = ref('Approved'); 

        const filteredDorms = computed(() => {
      if (selectedStatus.value === 'Approved') {
        return dorms.value.filter(x => x.status === 'approved');
      } else if (selectedStatus.value === 'Declined') {
        return dorms.value.filter(x => x.status != 'approved' && x.status != 'pending');
      } else if (selectedStatus.value === 'Pending') {
        return dorms.value.filter(x => x.status === 'pending');
      }
    });
        
            
       


        return {
            selectedStatus,
            filteredDorms,
            user,
            id,
            address,
            lat,
            long,
            detailed_address,
            property_name,
            description,
            floors_total,
            rooms_total,
            rooms,
            amenities,
            short_term,
            mix_gender,
            curfew,
            curfew_hours,
            minimum_stay,
            rules,
            dorm_image,
            dorm_image_src,
            business_permit_image,
            business_permit_image_src,
            termsAndCondition,
            isMobileView,
            active,
            progress,
            errors,
            dorms,
            loading,
            openFormModal,
            closeFormModal,
            getCoordinates,
            mapDrag,
            changeAddress,
            addRoom,
            removeRoom,
            removeRule,
            addRule,
            addAmenities,
            roomImageClick,
            roomImageChange,
            dormImageClick,
            dormImageChange,
            bpImageClick,
            bpImageChange,
            saveDorm,
            validationError,
            editDorm,
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="px-4 pt-14 lg:ml-64">
            <div class="flex items-center justify-start mt-4">
                <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zM272 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/></svg>                </span>
                <h3 class="text-3xl">Manage Dormitories</h3>
            </div>
            <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />

            <div class="flex flex-col pr-5 pt-5">
                <div class="w-full">
                    <p class="float-right">
                        <button
                            class="bg-orange-400 text-white rounded-md py-2 px-5"
                            @click="[openFormModal(), (active = 0)]"
                        >
                            ADD DORM
                        </button>
                    </p>
                </div>
            </div>

            <div
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                dorms.filter((x) => {
                                    return (
                                        x.status != "approved" &&
                                        x.status != "pending"
                                    );
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Declined Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                dorms.filter((x) => {
                                    return x.status == "approved";
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Approved Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                dorms.filter((x) => {
                                    return x.status == "pending";
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Pending Dorm</p>
                    </div>
                </div>
                
            </div>

            <div class="my-10">
                <select
                class="w-40 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                v-model="selectedStatus"
                >
                <option value="Approved">Approved</option>
                <option value="Declined">Declined</option>
                <option value="Pending">Pending</option>
                </select>
            </div>

            <div
                className="
                pt-5
                grid
                grid-cols-1
                sm:grid-cols-2
                md:grid-cols-3
                lg:grid-cols-4
                xl:grid-cols-4
                2xl:grid-cols-6
                gap-8
          "
            >
                <DormList
                    :dorms.sync="filteredDorms"
                    :user.sync="user"
                    @edit-dorm="(dorm) => editDorm(dorm)"
                />
            </div>

            <!-- Modal -->
            <div
                id="dormModal"
                tabindex="-1"
                aria-hidden="true"
                class="fixed top-0 left-0 right-0 z-50 hidden shadow-md items-center justify-center min-h-screen p-4 overflow-x-hidden overflow-y-auto md:inset-0"
            >
                <div class="relative w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div
                        class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                    >
                        <!-- Modal header -->
                        <div
                            class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                        >
                            <h3
                                class="text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                Add New Dorm
                            </h3>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                @click="closeFormModal()"
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
                        <div class="p-6 space-y-6 dark:text-white">
                            <div
                                class="w-full flex justify-center items-center"
                                v-if="active == 0"
                            >
                                <div
                                    class=""
                                    style="height: 300px; width: 300px"
                                >
                                    <img
                                        src="/images/logo.png"
                                        alt="logo"
                                        style="width: 100%"
                                        class="mt-10"
                                    />
                                    <p
                                        class="text-2xl text-center mt-5 font-fold"
                                    >
                                        Welcome to Dormhub!
                                    </p>

                                    <p
                                        class="text-md text-center mt-5 font-fold"
                                    >
                                        Register your dorm free by answering the
                                        following questions.
                                    </p>

                                </div>


                            </div>
                            <div v-if="active==0">
                                <div class="w-full mt-2">
                                    <input type="checkbox" value="I guarantee that all details provided are accurate and true." v-model="termsAndCondition">
                                    <label class="ml-2 text-sn">* I guarantee that all details provided are accurate and true.</label>
                                </div>

                                <div class="w-full mt-2">
                                    <input type="checkbox" value="I agree with the terms and condition." v-model="termsAndCondition">
                                    <label class="ml-2 text-sm">* I agree with the <span class="text-orange-400 cursor-pointer hover:underline">terms and condition</span></label>
                                </div>
                            </div>
                            <!--BusinessPermit-->
                            <div class="w-full" v-if="active == 1">
                                <p class="text-2xl font-bold mt-1 ml-2">
                                    Business Permit
                                </p>

                                <p class="text-xs mt-1 ml-2">
                                    Upload your business permit.
                                </p>

                                <div class="w-full mt-10 px-10">
                                    <input
                                                type="file"
                                                id="business_permit"
                                                class="hidden"
                                                @change="bpImageChange($event)"
                                                accept="image/*"
                                            />

                                            <label
                                                for="business_permit"
                                                class="relative cursor-pointer"
                                            >
                                                <div
                                                    class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                >
                                                    <img
                                                        v-if="business_permit_image_src "
                                                        :src="business_permit_image_src "
                                                        alt="business permit"
                                                        class="h-48 w-auto rounded-lg"
                                                    />
                                                    <span v-else
                                                        >Input Image</span
                                                    >
                                                </div>
                                            </label>
                                </div>


                                <p class="text-xs text-red-500 ml-2 mt-4">{{validationError('business_permit_image', errors)}} </p>

                            </div>
                            <!--Dorm Image-->
                            <div class="w-full" v-if="active == 2">
                                <p class="text-2xl font-bold mt-1 ml-2">
                                    Dorm Image
                                </p>

                                <p class="text-xs mt-1 ml-2">
                                    Allow your seeker to see your dorm
                                </p>

                                <div class="w-full mt-10 px-10">
                                    <input
                                                type="file"
                                                id="dorm_image"
                                                class="hidden"
                                                @change="dormImageChange($event)"
                                                accept="image/*"
                                            />

                                            <label
                                                for="dorm_image"
                                                class="relative cursor-pointer"
                                            >
                                                <div
                                                    class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                >
                                                    <img
                                                        v-if="dorm_image_src "
                                                        :src="dorm_image_src"
                                                        alt="dorm_image"
                                                        class="h-48 w-auto rounded-lg"
                                                    />
                                                    <span v-else
                                                        >Input Image</span
                                                    >
                                                </div>
                                            </label>
                                </div>


                                <p class="text-xs text-red-500 ml-2 mt-4">{{validationError('business_permit_image', errors)}} </p>

                            </div>
                            <!--Address-->
                            <div class="flex flex-col w-full" v-if="active == 3" >
                                <div class="w-full p-2">
                                <InputLabel for="address" value="Map Address" />

                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="address"
                                    required
                                    autocomplete="address"
                                    @input="changeAddress($event)"
                                />
                                <span class="text-xs text-red-500 ml-2">{{validationError('map_address', errors)}} </span>
                            </div>

                            <div class="w-full p-2">
                                <InputLabel for="detailed_address" value="Detailed Address" />

                                <TextInput
                                    id="detailed_address"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="detailed_address"
                                    required
                                    autocomplete="detailed_address"
                                    placeholder="House No., Street, Barangay, Municipaluty, Province, Region"
                                />
                                <span class="text-xs text-red-500 ml-2">{{validationError('detailed_address', errors)}} </span>
                            </div>

                            <div class="w-full p-2">
                                <MapboxMap
                                    style="height: 310px"
                                    access-token="pk.eyJ1IjoiYmFsb2dzeHh4IiwiYSI6ImNsbHA1dDN2MDAydGczZXFqZHprcW44dXIifQ.Z0dcyAB1W1B4-jcaqC_NKA"
                                    map-style="mapbox://styles/mapbox/streets-v11"
                                    :center="[long, lat]"
                                    :zoom="15"
                                >
                                    <MapboxMarker :lng-lat="[long, lat]"  :draggable="true" @mb-dragend="mapDrag($event)"/>
                                </MapboxMap>
                            </div>
                            </div>
                            <!--Dorm Desc-->
                            <div class="flex flex-col w-full" v-if="active == 4">
                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full mx-1">
                                    <InputLabel for="property_name" value="Dorm Name" />

                                    <TextInput
                                        id="property_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="property_name"
                                        required
                                        autocomplete="name"
                                        placeholder="Dorm Name"
                                    />
                                    <span class="text-xs text-red-500 ml-2">{{validationError('property_name', errors)}} </span>
                                </div>

                                <div class="w-full mx-1">
                                    <InputLabel for="description" value="Describe your dorm ?" />

                                    <TextInput
                                        id="description"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="description"
                                        required
                                        autocomplete="description"
                                        placeholder="Describe your dorm ?"
                                    />

                                    <span class="text-xs text-red-500 ml-2">{{validationError('description', errors)}} </span>
                                </div>
                            </div>

                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full mx-1">
                                    <InputLabel for="floors_total" value="How many floors in total ?" />

                                    <TextInput
                                        id="floors_total"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="floors_total"
                                        required
                                        autocomplete="floors_total"
                                        placeholder="No. of Floors"
                                    />

                                    <span class="text-xs text-red-500 ml-2">{{validationError('floors_total', errors)}} </span>
                                </div>

                                <div class="w-full mx-1">
                                    <InputLabel for="rooms_total" value="How many rooms in total ?" />

                                    <TextInput
                                        id="rooms_total"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="rooms_total"
                                        required
                                        autocomplete="rooms_total"
                                        placeholder="No. of Rooms"
                                    />

                                    <span class="text-xs text-red-500 ml-2">{{validationError('rooms_total', errors)}} </span>
                                </div>
                            </div>

                            <div class="w-full px-3">
                                <div class="w-ful flex flex-col p-2 mt-2"
                                    style="border: 1px solid black; border-radius: 5px;"
                                    v-for="(room, index) in rooms" :key="index"
                                >
                                    <div class="w-full">
                                    <span class="float-right cursor-pointer"
                                            @click="removeRoom(index)"
                                    >
                                            <i class="fa-solid fa-trash-can"></i>
                                    </span>
                                    </div>

                                            <InputLabel value="Room Image" />

                                            <input type="file" :id="'room_image' + index" :ref="'room_image_' + index" style="display: none"
                                                @change="roomImageChange($event, 'room_image' + index, index)"
                                            />

                                            <img :src="room.src ?? '/images/upload_image.png'" alt="upload_image"
                                                class="cursor-pointer"
                                                @click="roomImageClick('room_image' + index)"
                                                style="border: 1px solid black; border-radius: 5px; height: 235px; width: 100%;"
                                            >






                                    <div class="w-full flex flex-row mt-3">
                                            <div class="w-full mx-1">
                                                <InputLabel for="name" value="Room Name" />

                                                <TextInput
                                                    id="name"
                                                    type="text"
                                                    class="block w-full"
                                                    v-model="room.name"
                                                    required
                                                    placeholder="Room Name"
                                                />
                                            </div>

                                            <div class="w-full mx-1">
                                                <InputLabel for="type_of_room" value="Capacity" />

                                                <select v-model="room.type_of_room" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="Room for 1">Room for 1</option>
                                                    <option value="Room for 2">Room for 2</option>
                                                    <option value="Room for 3">Room for 3</option>
                                                    <option value="Room for 4">Room for 4</option>
                                                    <option value="Room for 5">Room for 5</option>
                                                </select>
                                            </div>

                                            <div class="w-full mx-1">
                                                <InputLabel for="furnished_type" value="Fursnished Type" />

                                                <select v-model="room.furnished_type" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="Furnished">Furnished</option>
                                                    <option value="Semifurnised">Semifurnised</option>
                                                    <option value="Bare">Bare</option>
                                                </select>
                                            </div>

                                            <div class="w-full mx-1">
                                                <InputLabel for="is_aircon" value="Aircon" />

                                                <select v-model="room.is_aircon" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>

                                    <div class="w-full flex flex-row mt-3">
                                        <div class="w-full mr-1">
                                            <InputLabel for="fee" value="Monthly Fee" />

                                            <TextInput
                                                id="fee"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="room.fee"
                                                required
                                            />
                                        </div>

                                        <div class="w-full mr-1">
                                            <InputLabel for="deposit" value="Deposit Fee" />

                                            <TextInput
                                                id="deposit"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="room.deposit"
                                                required
                                            />
                                        </div>

                                        <div class="w-full">
                                            <InputLabel for="advance" value="Advance Fee" />

                                            <TextInput
                                                id="advance"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="room.advance"
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-2">
                                <button class="py-2 px-4 bg-orange-500 ml-3 mt-1 rounded-md"
                                    @click="addRoom()"
                                >
                                    Add Room
                                </button>
                            </div>
                            </div>
                            <!--Rules-->
                            <div class="w-full" v-if="active == 5">
                            <p class="text-2xl font-bold mt-1 ml-2">
                                Rules
                            </p>

                            <p class="text-xs mt-1 ml-2">
                                Your house your rules. Define the dos and dont of your tenants.
                            </p>

                            <div class="w-full flex flex-col gap-3 mt-10">
                                <div class="w-full px-1">
                                    <InputLabel value="Is short-term stayed allowed ?" class="text-xs"/>

                                    <select v-model="short_term" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <span class="text-xs text-red-500 ml-2">{{validationError('short_term', errors)}} </span>
                                    <div class="my-4"
                                    v-if="short_term=='Yes'">
                                        <InputLabel value="What is the minimum stayed allowed ?" class="text-xs"/>

                                        <TextInput
                                            id="minimum_stay"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="minimum_stay"
                                            required
                                            autocomplete="minimum_stay"
                                        />
                                        <span class="text-xs text-red-500 ml-2">{{validationError('minimum_stay', errors)}} </span>
                                    </div>
                                    <div class="my-4 hidden"
                                    v-else>
                                        <InputLabel value="What is the minimum stayed allowed ?" class="text-xs"/>

                                        <TextInput
                                            id="minimum_stay"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="minimum_stay"
                                            required
                                            autocomplete="minimum_stay"
                                        />

                                    </div>
                                </div>

                                <div class="w-full px-1">
                                    <InputLabel value="Is Co-ed mixed gender allowed ?" class="text-xs"/>

                                    <select v-model="mix_gender" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <span class="text-xs text-red-500 ml-2">{{validationError('mix_gender', errors)}} </span>

                                </div>

                                <div class="w-full px-1">
                                    <InputLabel value="Do you have a curfew ?" class="text-xs"/>

                                    <select v-model="curfew" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <span class="text-xs text-red-500 ml-2">{{validationError('curfew', errors)}} </span>

                                    <div class="my-4"
                                        v-if="curfew=='Yes'"
                                    >
                                        <InputLabel value="What is the curfew hours ?" class="text-xs"/>

                                        <TextInput
                                            id="curfew_hours"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="curfew_hours"
                                            required
                                            autocomplete="curfew_hours"
                                        />
                                        <span class="text-xs text-red-500 ml-2">{{validationError('curfew_hours', errors)}}
                                        </span>
                                    </div>
                                    <div class="my-4 hidden"
                                        v-else>
                                        <InputLabel value="What is the curfew hours ?" class="text-xs"/>

                                    <TextInput
                                        id="curfew_hours"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="curfew_hours"
                                        required
                                        autocomplete="curfew_hours"
                                    />
                                    </div>
                                </div>
                            </div>


                            <div class="w-full flex flex-col mt-5 px-1">
                                <div class="w-full flex flex-row mt-2" v-for="(rule, index) in rules" :key="index">

                                    <div style="width: 95%">
                                        <InputLabel value="Rule" class="text-xs"/>

                                        <TextInput
                                            id="rule"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="rule.name"
                                            required
                                        />
                                    </div>

                                    <div style="width: 5%"
                                        class="flex justify-center items-center mt-5"
                                    >
                                        <span class="cursor-pointer"
                                            @click="removeRule(index)"
                                         >
                                            <i class="fa-solid fa-trash-can"></i>
                                        </span>
                                    </div>


                                </div>
                            </div>

                            <div class="w-full px-1 ">
                                <button class="py-2 px-4 bg-cyan-500 mt-1 rounded-md"
                                    @click="addRule()"
                                >
                                    Add Rule
                                </button>
                            </div>

                            </div>
                            <!--Amenities-->
                            <div class="w-full" v-if="active == 6">
                            <p class="text-2xl font-bold mt-1 ml-3">
                                Amenities
                            </p>

                            <p class="text-xs mt-1 ml-3">
                                Select all apply
                            </p>


                            <div class="w-full flex flex-row mt-5">
                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('WIFI')"
                                        :class="{'bg-gray-300': amenities.includes('WIFI')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        WIFI
                                    </button>
                                </div>

                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Security')"
                                        :class="{'bg-gray-300': amenities.includes('Security')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Security
                                    </button>
                                </div>
                            </div>

                            <div class="w-full flex flex-row mt-5">
                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Cleaning Service')"
                                        :class="{'bg-gray-300': amenities.includes('Cleaning Service')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Cleaning Service
                                    </button>
                                </div>

                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Parking')"
                                        :class="{'bg-gray-300': amenities.includes('Parking')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Parking
                                    </button>
                                </div>
                            </div>


                            <div class="w-full flex flex-row mt-5">
                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Mailboxes')"
                                        :class="{'bg-gray-300': amenities.includes('Mailboxes')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Mailboxes
                                    </button>
                                </div>

                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Vending Machines')"
                                        :class="{'bg-gray-300': amenities.includes('Vending Machines')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Vending Machines
                                    </button>
                                </div>
                            </div>


                            <div class="w-full flex flex-row mt-5">
                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Laundry')"
                                        :class="{'bg-gray-300': amenities.includes('Laundry')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Laundry
                                    </button>
                                </div>

                                <div class="w-full m-2">
                                    <button class="py-2 px-4 rounded-sm w-full"
                                        @click="addAmenities('Pet Friendly')"
                                        :class="{'bg-gray-300': amenities.includes('Pet Friendly')}"
                                        style="border: 1px solid black; height: 60px;"
                                    >
                                        Pet Friendly
                                    </button>
                                </div>
                            </div>
                        </div>


                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                        >
                            <div v-if="active == 0">
                                <button
                                    data-modal-hide="defaultModal"
                                    type="button"
                                    :disabled="termsAndCondition.length < 2"
                                    :class="{
                                        'cursor-not-allowed':
                                            termsAndCondition.length < 2,
                                        'bg-cyan-500 text-white':
                                            termsAndCondition.length == 2,
                                    }"
                                    @click="active = 1"
                                    class=" border text-black focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                >
                                    Start
                                </button>
                            </div>

                            <div class="w-full" v-else>

                                <button
                                    data-modal-hide="defaultModal"
                                    v-if="active > 1"
                                    :disabled="active == 1"
                                    :class="{
                                        'cursor-not-allowed': active == 1,
                                    }"
                                    @click="active > 1 ? active-- : ''"
                                    type="button"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                >
                                    Back
                                </button>
                                <button
                                    data-modal-hide="defaultModal"
                                    v-if="active < 6"
                                    @click="active++"
                                    type="button"
                                    class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                >
                                    Next
                                </button>
                                <button
                                    data-modal-hide="defaultModal"
                                    v-if="active == 6"
                                    @click="saveDorm()"
                                    type="button"
                                    :disabled="loading"
                                    :class="{'cursor-not-allowed': loading}"
                                    class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                >
                                    {{!!loading ? 'Saving...' : 'Submit'}}
                                    <pulse-loader :loading="loading"></pulse-loader>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Modal Content */

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
