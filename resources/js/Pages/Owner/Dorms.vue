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
import Editor from "@tinymce/tinymce-vue";
import VsToast from '@vuesimple/vs-toast';

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
        Editor,
        VsToast,
    },
    setup() {
        const page = usePage();

        const user = computed(() => page.props.auth.user);

        const data = new FormData();
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
        const rooms_total = ref();
        const rooms = ref([]);
        const commonAreas = ref([]);
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
        const terms = ref("");
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

        const roomAreasClick = (arg) => {
            document.getElementById(arg).click();
        };

        const roomAreasChange = (e, arg, index) => {
            const image = e.target.files[0];

            commonAreas.value[index].image = image;

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                commonAreas.value[index].src = e.target.result;
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
            const totalRooms = parseInt(rooms_total.value);
            // Check if the total number of rooms added is equal to rooms_total
            if (rooms.value.length === totalRooms) {
                VsToast.show({
                    title: 'Warning',
                    message: 'You can only add exactly ' + totalRooms + ' rooms. Please change total of rooms if you want to add more rooms',
                    variant: 'warning',
                });
                console.error('Please add exactly ' + totalRooms + ' rooms.');
            } else {
                rooms.value.push({
                    id: null,
                    name: null,
                    type_of_room: null,
                    is_aircon: null,
                    furnished_type: null,
                    image: null,
                    src: null,
                    fee: null,
                    advance: null,
                    deposit: null,
                    is_available: null,
                });
            }
        };

        const removeRoom = (index) => {
            rooms.value = rooms.value.filter((x, i) => {
                return i != index;
            });
        };

        const addCommonAreas = () => {
            if (commonAreas.value.length < 5) {
                commonAreas.value.push({
                id: null,
                name: null,
                image: null,
                src: null,
                });
            } else {
                VsToast.show({
                    title: 'Warning',
                    message: 'You can only add up to 5 common areas.',
                    variant: 'warning',
                });
                console.log();
            }
        };

        const removeCommonAreas = (index) => {
            commonAreas.value = commonAreas.value.filter((x, i) => {
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
            commonAreas.value = dorm.common_areas;
            terms.value = dorm.terms;
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
            if(validateTerms()){
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
                    data.append("terms", terms.value);
                    data.append("floors_total", floors_total.value);
                    data.append("rooms_total", rooms_total.value);
                    data.append("dorm_image", dorm_image.value);
                    data.append("business_permit_image_src", business_permit_image_src.value);

                    // Rooms Table
                    data.append("rooms", JSON.stringify(rooms.value));

                    data.append("commonAreas", JSON.stringify(commonAreas.value));

                    // Amenities Table
                    data.append("amenities", JSON.stringify(amenities.value));

                    // Rules Table
                    data.append("short_term", short_term.value);
                    data.append("mix_gender", mix_gender.value);
                    data.append("curfew", curfew.value);
                    data.append("curfew_hours", curfew_hours.value);
                    data.append("minimum_stay", minimum_stay.value);
                    data.append("rules", JSON.stringify(rules.value));

                    const apiUrl = !!id.value ? route("update.dorm", { id: id.value }) : route("save.dorm");

                    axios
                        .post(apiUrl, data)
                        .then((response) => {
                            loading.value = false;

                            swal(
                                "Success!",
                                "Your dorm has been save.",
                                "success"
                            );

                            setTimeout(function () {

                            }, 1500);
                        })
                        .catch((error) => {
                            errors.value = error.response.data.errors;
                            loading.value = false;
                        });
                }
            );
            }else{
                VsToast.show({
                    title: 'Warning',
                    message: 'Please add terms & condition for dorm',
                    variant: 'warning',
                });
            }
            
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

        const errorMessages = ref({
            property_name: '',
            description: '',
            floors_total: '',
            rooms_total: '',
            business_permit_image_src:'',
            dorm_image_src:'',
            address:'',
            short_term:'',
            mix_gender:'',
            curfew_hours:'',
            detailed_address:'',
            terms:'',
            commonArea: [],
            room: [],
        // Add other properties for common areas if needed
        });
        //validationn
        const validateStep = () => {
            let isValid = true;
            const roomValidation = validateRooms();

            switch (active.value) {
                case 1: 
                    isValid = validateDormDetails();
                    break;
                case 2: 
                    isValid = roomValidation && validateRoomsTotal();
                    break;
                case 3: 
                    isValid = validateBusinessPermit();
                break;
                case 4:
                    isValid = validateDormImage();
                break;
                case 5: 
                    isValid = validateAddress();
                break;
                case 6: 
                    isValid = validateRules();
                break;
                case 7: 
                    isValid = validateAmenities();
                break;
            default:
                isValid = true, 
                errors= {} ;
            }
            return isValid;
        };

        const validateTerms = () => {
            let isValid = true;
            errorMessages.value.terms='';


            if(terms.value.trim() === ''){
                isValid = false;
                errorMessages.value.terms = 'Please add your dorm terms & condition';
            }

            return isValid;

        }

        const validateRules = () => {
            let isValid = true;
            errorMessages.value.short_term = '';
            errorMessages.value.mix_gender = '';
            errorMessages.value.curfew_hours = '';

            if (short_term.value.trim() === '') {
                isValid = false;
                errorMessages.value.short_term = 'Select minimum stay.';
            }

            if (mix_gender.value.trim() === '') {
                isValid = false;
                errorMessages.value.mix_gender = 'Select mix gender.';
            }

            if (curfew.value.trim() === '') {
                isValid = false;
                errorMessages.value.curfew = 'Select curfew hours.';
            }
                
            return isValid;
        };

        const validateAmenities = () => {
            let isValid = true;
            const minAmenities = 2;
            errorMessages.value.amenities='';


            if(amenities.value.length <= minAmenities){
                isValid = false;
                VsToast.show({
                    title: 'Warning',
                    message: 'Please select more than 2 amenities',
                    variant: 'warning',
                });
            }
            console.log(parseInt(amenities.value.length))
            return isValid;

        }

        const validateAddress = () => {
            let isValid = true;
            errorMessages.value.address = '';
            errorMessages.value.detailed_address = '';

            if (address.value.trim() === '') {
                isValid = false;
                errorMessages.value.address = 'Map Address is required.';
            }

            if (detailed_address.value.trim() === '') {
                isValid = false;
                errorMessages.value.detailed_address = 'Detailed address is required.';
            }
                
            return isValid;
        };

        const validateRoomsTotal = () => {
            const totalRooms = parseInt(rooms_total.value);

            if (rooms.value.length !== totalRooms) {
                VsToast.show({
                    title: 'Warning',
                    message: 'Please add exactly ' + totalRooms + ' rooms.',
                    variant: 'warning',
                });
                console.error('Please add exactly ' + totalRooms + ' rooms.');
                return false;
            }

            return true;
        };

        const validateBusinessPermit = () =>{
            let isValid = true;
            errorMessages.value.business_permit_image_src ='';

            if(business_permit_image_src.value === null){
                isValid = false;
                errorMessages.value.business_permit_image_src = 'Image is required.';
            }
            return isValid;
        };

        const validateDormImage = () =>{
            let isValid = true;
            errorMessages.value.dorm_image_src ='';

            if(dorm_image_src.value === null){
                isValid = false;
                errorMessages.value.dorm_image_src = 'Image is required.';
            }
            return isValid;
        };

        const validateDormDetails = () => {
            let isValid = true;
            errorMessages.value.property_name = '';
            errorMessages.value.description = '';
            errorMessages.value.floors_total = '';
            errorMessages.value.rooms_total = '';
            errorMessages.value.commonArea =[];

            if (property_name.value.trim() === '') {
                isValid = false;
                errorMessages.value.property_name = 'Dorm Name is required.';
            }

            if (description.value.trim() === '') {
                isValid = false;
                errorMessages.value.description = 'Description is required.';
            }

            if (floors_total.value === '') {
                isValid = false;
                errorMessages.value.floors_total = 'Number of floors is required.';
            }

            if (rooms_total.value == null || rooms_total.value ==='') {
                isValid = false;
                errorMessages.value.rooms_total = 'Number of rooms is required.';
            }
            if (active.value === 1) {
                errorMessages.value.commonArea = [];

                commonAreas.value.forEach((commonArea, index) => {
                    // Check if commonArea is defined
                    if (commonArea) {
                    // Validate if there's data for both description and image
                    const hasDescription = commonArea.name && commonArea.name.trim() !== '';
                    const hasImage = commonArea.image !== null;

                    // Initialize error messages for the current common area
                    errorMessages.value.commonArea[index] = {
                        name: '',
                        src: '',
                        // Add other properties if needed
                    };

                    if (!hasDescription) {
                        isValid = false;
                        // Set error message for the current common area
                        errorMessages.value.commonArea[index].name = 'Description is required.';
                    }

                    if (!hasImage) {
                        isValid = false;
                        // Set error message for the current common area
                        errorMessages.value.commonArea[index].src = 'Image is required.';
                    }
                    }
                });

                }
                    return isValid;
                };

        const validateRooms = () => {
            let isValid = true;
            errorMessages.value.room =[];
           
            if (active.value === 2) {
                errorMessages.value.room = [];
                rooms.value.forEach((room, index) => {
                if (room) {
                    const hasName = room.name && room.name.trim() !== '';
                    const hasFee = room.fee !== null;
                    const hasAdvance = room.advance !== null;
                    const hasDeposit = room.deposit !== null;
                    const hasTypeOfRoom =  room.type_of_room && room.type_of_room.trim()!== '' ;
                    const hasAircon = room.is_aircon && room.is_aircon.trim() !== '';
                    const hasFurniture = room.furnished_type && room.furnished_type.trim() !=='';
                    const hasImage = room.src !== null;

                    errorMessages.value.room[index] = {
                        name: '',
                        src: '',
                        fee: '',
                        advance: '',
                        deposit: '',
                        type_of_room: '',
                        is_aircon: '',
                        furnished_type: '',
                    };

                    if (!hasImage) {
                        isValid = false;
                        errorMessages.value.room[index].src = 'Image is required.';
                    }

                    if(!hasFee){
                        isValid = false;
                        errorMessages.value.room[index].fee = 'Monthly fee is required.';
                    }

                    if(!hasAdvance){
                        isValid = false;
                        errorMessages.value.room[index].advance = 'Advance fee is required.';
                    }

                    if(!hasDeposit){
                        isValid = false;
                        errorMessages.value.room[index].deposit = 'Deposit fee is required.';
                    }

                    if(!hasTypeOfRoom){
                        isValid = false;
                        errorMessages.value.room[index].type_of_room = 'Please select the capacity.';
                    }

                    if(!hasAircon){
                        isValid = false;
                        errorMessages.value.room[index].is_aircon = 'Select aircondition or non-aircondition';
                    }

                    if(!hasFurniture){
                        isValid = false;
                        errorMessages.value.room[index].furnished_type = 'Select funished type';
                    }
                    
                    if (!hasName) {
                        isValid = false;
                        errorMessages.value.room[index].name = 'Room Name is required.';
                    }
                }
            });
            }
    
        return isValid;
        };
        

        // Function to handle clicking "Next"
        const handleNext = () => {
        if (validateStep()) {
            // Validation passed, proceed to the next step
            active.value++;
        } else {
        }
        };
        
        return {
            errorMessages,
            selectedStatus,
            filteredDorms,
            handleNext,
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
            terms,
            active,
            progress,
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
            commonAreas,
            roomAreasClick,
            roomAreasChange,
            addCommonAreas,
            removeCommonAreas,
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="px-4 pt-14 lg:ml-64">
            <div class="flex items-center justify-start mt-4">
                <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zM272 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/></svg>                </span>
                <h3 class="text-3xl">Manage Dormitories</h3>
            </div>
            <hr class="h-px my-5 bg-orange-400 border-1" />

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
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 "
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
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 "
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
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 "
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
                lg:grid-cols-3
                xl:grid-cols-4
                2xl:grid-cols-5
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
                style="background-color: rgba(0, 0, 0, 0.7)"
                aria-hidden="true"
                class="fixed top-0 left-0 right-0 z-50 hidden shadow-md items-center justify-center min-h-screen p-4 overflow-x-hidden overflow-y-auto md:inset-0"
            >
                <div class="relative w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div
                        class="relative bg-white rounded-lg shadow"
                    >
                        <!-- Modal header -->
                        <div
                            class="flex items-start justify-between p-4 border-b rounded-t "
                        >
                            <h3
                                class="text-xl font-semibold text-gray-900"
                            >
                                Add New Dorm
                            </h3>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center "
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
                        <div class="p-6 space-y-6">
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
                            <div v-if="active==8">
                                <div>
                                    <div class="text-md text-black mb-4">Terms & Condition:</div>
                                    <Editor
                                        id="content"
                                        api-key="rnwni8gfoofnq592kqlityphztlce2nvzunwxpqqs3a0y8dv"
                                        v-model="terms"
                                        :init="{
                                            menubar: false,
                                            plugins: 'lists link image emoticons',
                                            toolbar:
                                                'undo redo | blocks | ' +
                                                'bold italic backcolor | alignleft aligncenter ' +
                                                'alignright alignjustify | bullist numlist outdent indent | ' +
                                                'removeformat | help',
                                            content_style:
                                                'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                                        }"
                                    />
                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.terms }} </span>
                                </div>
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
                            <div class="w-full" v-if="active == 3">
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
                                                    :class="{ 'border-red-500': !!errorMessages.business_permit_image_src }"
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
                                            <span class="text-xs text-red-500 ml-2">{{  errorMessages.business_permit_image_src }} </span>
                                </div>


                                <p class="text-xs text-red-500 ml-2 mt-4">{{validationError('business_permit_image', errors)}} </p>

                            </div>
                            <!--Dorm Image-->
                            <div class="w-full" v-if="active == 4">
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
                                                    :class="{ 'border-red-500': !!errorMessages.dorm_image_src }"
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
                                            <span class="text-xs text-red-500 ml-2">{{  errorMessages.dorm_image_src }} </span>
                                </div>


                                <p class="text-xs text-red-500 ml-2 mt-4">{{validationError('business_permit_image', errors)}} </p>

                            </div>
                            <!--Address-->
                            <div class="flex flex-col w-full" v-if="active == 5" >
                                <div class="w-full p-2">
                                <label class="text-black" for="address">Map Address</label>

                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full text-black"
                                    v-model="address"
                                    :class="{ 'border-red-500': !!errorMessages.address }"
                                    required
                                    autocomplete="address"
                                    @input="changeAddress($event)"
                                />
                                <span class="text-xs text-red-500 ml-2">{{  errorMessages.address }} </span>
                            </div>

                            <div class="w-full p-2">
                                <InputLabel class="text-black" for="detailed_address" value="Detailed Address" />

                                <TextInput
                                    id="detailed_address"
                                    type="text"
                                    class="mt-1 block w-full text-black"
                                    v-model="detailed_address"
                                    :class="{ 'border-red-500': !!errorMessages.detailed_address }"
                                    required
                                    autocomplete="detailed_address"
                                    placeholder="House No., Street, Barangay, Municipaluty, Province, Region"
                                />
                                <span class="text-xs text-red-500 ml-2">{{  errorMessages.detailed_address }} </span>
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
                            <div class="flex flex-col w-full" v-if="active == 1">
                                <p class="text-2xl font-bold mt-1 ml-2 mb-2">
                                    Step 1: Dorm Details
                                </p>
                            <div class="w-full px-2 mb-2 flex flex-row">
                            
                                <div class="w-full mx-1">
                                    <InputLabel class="text-black" for="property_name" value="Dorm Name" />

                                    <TextInput
                                        id="property_name"
                                        type="text"
                                        class="mt-1 block w-full text-black"
                                        v-model="property_name"
                                        :class="{ 'border-red-500': !!errorMessages.property_name }"
                                        required
                                        autocomplete="name"
                                        placeholder="Dorm Name"
                                    />
                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.property_name }} </span>
                                </div>

                                <div class="w-full mx-1">
                                    <InputLabel class="text-black" for="description" value="Describe your dorm ?" />

                                    <TextInput
                                        id="description"
                                        type="text"
                                        class="mt-1 block w-full text-black"
                                        v-model="description"
                                        :class="{ 'border-red-500': !!errorMessages.description }"
                                        required
                                        autocomplete="description"
                                        placeholder="Describe your dorm ?"
                                    />

                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.description }} </span>
                                </div>
                            </div>

                            <div class="w-full px-2 flex flex-row">
                                <div class="w-full mx-1">
                                    <InputLabel for="floors_total" class="text-black" value="How many floors in total ?" />

                                    <TextInput
                                        id="floors_total"
                                        type="number"
                                        class="mt-1 block w-full text-black"
                                        v-model="floors_total"
                                        :class="{ 'border-red-500': !!errorMessages.floors_total }"
                                        required
                                        autocomplete="floors_total"
                                        placeholder="No. of Floors"
                                    />

                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.floors_total }} </span>
                                </div>

                                <div class="w-full mx-1">
                                    <InputLabel class="text-black" for="rooms_total" value="How many rooms in total ?" />

                                    <TextInput
                                        id="rooms_total"
                                        type="number"
                                        class="mt-1 block w-full text-black"
                                        v-model="rooms_total"
                                        required
                                        :class="{ 'border-red-500': !!errorMessages.rooms_total }"
                                        autocomplete="rooms_total"
                                        placeholder="No. of Rooms"
                                    />

                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.rooms_total }} </span>
                                </div>
                            </div>
                            <div class="w-full mt-2">
                                    <button class="py-2 px-4 bg-orange-400 ml-3 mt-1 rounded-md text-white"
                                        @click="addCommonAreas()"
                                    >
                                        Add Common Areas
                                    </button>
                                </div>
                                <div class="w-full px-3 ">
                                    <div class="w-ful flex flex-col p-3 mt-2 border rounded-xl border-gray-400 "
                                        v-for="(commonArea, index) in commonAreas" :key="index"
                                    >   
                                        <div class="w-full ">
                                        <span class="float-right cursor-pointer"
                                                @click="removeCommonAreas(index)"
                                        >
                                                <i class="fa-solid fa-trash-can"></i>
                                        </span>
                                        <InputLabel class="text-black" for="decription_Areas" value="Description:" />

                                            <TextInput
                                                id="decription_Areas"
                                                type="text"
                                                class="mt-1 block w-full text-black"
                                                v-model="commonArea.name"
                                                :class="{ 'border-red-500': !!errorMessages.commonArea[index]?.name && !commonArea.name }"
                                                required
                                                placeholder="e.g Living Room"
                                            />
                                            <div v-if="errorMessages.commonArea[index]">
                                                    <p class="text-xs text-red-500 ml-2">{{ errorMessages.commonArea[index].name }}</p>
                                            </div>
                                           
                                                                                    
                                            </div>

                                                <InputLabel value="Common Areas Image" class="text-black"/>

                                                <input
                                                    type="file"
                                                    :id="'areas_image' + index" :ref="'areas_image_' + index" style="display: none"
                                                    @change="roomAreasChange($event, 'areas_image' + index, index)"
                                                    accept="image/*"
                                                />

                                                <label
                                                    :for="'areas_image' + index"
                                                    class="relative cursor-pointer"
                                                >
                                                    <div
                                                        class="h-80 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                        :class="{ 'border-red-500': !!errorMessages.commonArea[index]?.src && !commonArea.src }"
                                                    >
                                                        <img
                                                            v-if="commonArea.src"
                                                            :src="commonArea.src"
                                                            alt="areas_image"
                                                            class="h-80 w-full rounded-lg object-cover bg-no-repeat bg-center"
                                                            @click="roomAreasClick('areas_image' + index)"
                                                        />
                                                        <span v-else
                                                            >Click to Input Common Areas Image</span
                                                        >
                                                    </div> 
                                                        
                                                </label>
                                                <div v-if="errorMessages.commonArea[index]">
                                                    <p class="text-xs text-red-500 ml-2">{{ errorMessages.commonArea[index].src }}</p>
                                                </div>
                                               
                                    </div>
                                              
                                </div>
                           
                                
                            </div> 
                            <!--Rooms-->
                            <div class="w-full" v-if="active==2">
                                <p class="text-2xl font-bold mt-1 ml-2 mb-2">
                                    Step 2: Add rooms
                                </p>
                                <p class="text-sm text-gray-600 font-bold  ml-2 mb-2">
                                    The total number of rooms you have is {{ rooms_total }} rooms.
                                </p>
                                <div class="w-full px-3 ">
                                    <div class="w-ful flex flex-col p-3 mt-2 border rounded-xl border-gray-400 "
                                        v-for="(room, index) in rooms" :key="index"
                                    >   
                                        <div class="w-full ">
                                        <span class="float-right cursor-pointer"
                                                @click="removeRoom(index)"
                                        >
                                                <i class="fa-solid fa-trash-can"></i>
                                        </span>
                                        </div>

                                                <InputLabel value="Room Image" class="text-black"/>

                                                <input
                                                    type="file"
                                                    :id="'room_image' + index" :ref="'room_image_' + index" style="display: none"
                                                    @change="roomImageChange($event, 'room_image' + index, index)"
                                                    accept="image/*"
                                                    required
                                                />

                                                <label
                                                    :for="'room_image' + index"
                                                    class="relative cursor-pointer"
                                                >
                                                    <div
                                                        class="h-80 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                        :class="{ 'border-red-500': !!errorMessages.room[index]?.src && !room.src }"
                                                    >
                                                        <img
                                                            v-if="room.src"
                                                            :src="room.src"
                                                            alt="room_image"
                                                            class="h-80 w-full rounded-lg object-cover bg-no-repeat bg-center"
                                                            @click="roomImageClick('room_image' + index)"
                                                        />
                                                        <span v-else
                                                            >Click to Input Room Image</span
                                                        >
                                                    </div>
                                                </label>
                                                <div v-if="errorMessages.room[index]">
                                                    <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].src }}</p>
                                                </div>

                                        <div class="w-full mt-3">
                                            <div class="flex flex-row mt-3">
                                                <div class="w-full mx-1">
                                                    <InputLabel class="text-black" for="name" value="Room Name" />

                                                    <TextInput
                                                        id="name"
                                                        type="text"
                                                        class="block w-full text-black"
                                                        v-model="room.name"
                                                        required
                                                        placeholder="Room Name"
                                                    />
                                                    <div v-if="errorMessages.room[index]">
                                                        <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].name }}</p>
                                                    </div>
                                                </div>

                                                <div class="w-full mx-1">
                                                    <InputLabel class="text-black" for="type_of_room" value="Capacity" />

                                                    <select required v-model="room.type_of_room" class="w-full border-gray-300  text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">                                                       
                                                        <option value="Room for 1">Room for 1</option>
                                                        <option value="Room for 2">Room for 2</option>
                                                        <option value="Room for 3">Room for 3</option>
                                                        <option value="Room for 4">Room for 4</option>
                                                        <option value="Room for 5">Room for 5</option>
                                                    </select>

                                                    <div v-if="errorMessages.room[index]">
                                                        <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].type_of_room }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-row mt-3">
                                                <div class="w-full mx-1">
                                                    <InputLabel class="text-black" or="furnished_type" value="Fursnished Type" />

                                                    <select required v-model="room.furnished_type" class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                        <option value="Furnished">Furnished</option>
                                                        <option value="Semifurnised">Semifurnised</option>
                                                        <option value="Bare">Bare</option>
                                                    </select>
                                                    <div v-if="errorMessages.room[index]">
                                                        <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].furnished_type }}</p>
                                                    </div>
                                                </div>

                                                <div class="w-full mx-1">
                                                    <InputLabel for="is_aircon" class="text-black" value="Aircon" />

                                                    <select required v-model="room.is_aircon" class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>

                                                    <div v-if="errorMessages.room[index]">
                                                        <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].is_aircon }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                                

                                                
                                            </div>

                                        <div class="w-full flex flex-row mt-3">
                                            <div class="w-full mx-1">
                                                <InputLabel for="fee" class="text-black" value="Monthly Fee" />

                                                <TextInput
                                                    id="fee"
                                                    type="number"
                                                    class="mt-1 block w-full text-black"
                                                    placeholder="0"
                                                    v-model="room.fee"
                                                    required
                                                />
                                                <div v-if="errorMessages.room[index]">
                                                    <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].fee }}</p>
                                                </div>
                                            </div>

                                            <div class="w-full mx-1">
                                                <InputLabel for="deposit" class="text-black" value="Deposit Fee" />

                                                <TextInput
                                                    id="deposit"
                                                    type="number"
                                                    placeholder="0"
                                                    class="mt-1 block w-full text-black"
                                                    v-model="room.deposit"
                                                    required
                                                />
                                                <div v-if="errorMessages.room[index]">
                                                    <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].deposit }}</p>
                                                </div>
                                            </div>

                                            <div class="w-full mx-1">
                                                <InputLabel for="advance" class="text-black" value="Advance Fee" />

                                                <TextInput
                                                    id="advance"
                                                    type="number"
                                                    placeholder="0"
                                                    class="mt-1 block w-full text-black"
                                                    v-model="room.advance"
                                                    required
                                                />
                                                <div v-if="errorMessages.room[index]">
                                                    <p class="text-xs text-red-500 ml-2">{{ errorMessages.room[index].advance }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full mt-2">
                                    <button class="py-2 px-4 bg-orange-400 ml-3 mt-1 rounded-md text-white"
                                        @click="addRoom()"
                                    >
                                        Add Room
                                    </button>
                                </div>
                            </div>
                            <!--Rules-->
                            <div class="w-full" v-if="active == 6">
                            <p class="text-2xl font-bold mt-1 ml-2 text-black">
                                Rules
                            </p>

                            <p class="text-xs mt-1 ml-2 text-black">
                                Your house your rules. Define the dos and dont of your tenants.
                            </p>

                            <div class="w-full flex flex-col gap-3 mt-10">
                                <div class="w-full px-1">
                                    <InputLabel class="text-black" value="Is short-term stayed allowed ?" />

                                    <select v-model="short_term" class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.short_term }} </span>
                                    <div class="my-4"
                                    v-if="short_term=='Yes'">
                                        <InputLabel class="text-black" value="What is the minimum stayed allowed ?" />

                                        <TextInput
                                            id="minimum_stay"
                                            type="text"
                                            class="mt-1 block w-full text-black"
                                            v-model="minimum_stay"
                                            required
                                            autocomplete="minimum_stay"
                                            placeholder="e.g: 3monhts/1year"
                                        />
                                    </div>
                                    <div class="my-4 hidden"
                                    v-else>
                                        <InputLabel class="text-black" value="What is the minimum stayed allowed ?"/>

                                        <TextInput
                                            id="minimum_stay"
                                            type="text"
                                            class="mt-1 block w-full text-black"
                                            v-model="minimum_stay"
                                            required
                                            autocomplete="minimum_stay"
                                        />

                                    </div>
                                </div>

                                <div class="w-full px-1">
                                    <InputLabel class="text-black" value="Is Co-ed mixed gender allowed ?" />

                                    <select v-model="mix_gender" class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.mix_gender }} </span>

                                </div>

                                <div class="w-full px-1">
                                    <InputLabel class="text-black" value="Do you have a curfew ?" />

                                    <select v-model="curfew" class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <span class="text-xs text-red-500 ml-2">{{  errorMessages.curfew }} </span>
                                    <div v-if="curfew=='Yes'"
                                    >
                                        <InputLabel class="text-black" value="What is the curfew hours ?"/>

                                        <TextInput
                                            id="curfew_hours"
                                            type="text"
                                            class="block w-full text-black"
                                            v-model="curfew_hours"
                                            required
                                            autocomplete="curfew_hours"
                                            placeholder="e. 12am-3am"
                                        />
                                        <span class="text-xs text-red-500 ml-2">{{validationError('curfew_hours', errors)}}
                                        </span>
                                    </div>
                                    <div class="hidden mt-5"
                                        v-else>
                                        <InputLabel value="What is the curfew hours ?" class="text-xs"/>

                                    <TextInput
                                        id="curfew_hours"
                                        type="text"
                                        class="block w-full text-black"
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
                                        <InputLabel class="text-black" value="Rule" />

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
                            <div class="w-full" v-if="active == 7">
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
                            class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b "
                        >
                            <div v-if="active == 0">
                                <button
                                    data-modal-hide="defaultModal"
                                    type="button"
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
                                    v-if="active < 8"
                                    @click="handleNext()"
                                    type="button"
                                    class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                >
                                    Next
                                </button>
                                <button
                                    data-modal-hide="defaultModal"
                                    v-if="active == 8"
                                    @click="saveDorm()"
                                    type="button"
                                    :disabled="loading || termsAndCondition.length < 2"
                                    :class="{
                                        'cursor-not-allowed': loading || termsAndCondition.length < 2,
                                        'bg-orange-400 text-white hover:text-gray-700 hover:bg-orange-300': !loading && termsAndCondition.length === 2,

                                        }"
                                    class="text-gray-500 float-right focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-orange-400 focus:z-10"
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
