<script>
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";
import LvProgressBar from "lightvue/progress-bar";
import { router } from "@inertiajs/vue3";
import { usePage, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, reactive, watch, onMounted, computed } from "vue";
import Editor from "@tinymce/tinymce-vue";
import VsToast from '@vuesimple/vs-toast';
export default {
    components: {
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        ApplicationLogo,
        TextInput,
        InputLabel,
        router,
        MapboxMap,
        MapboxMarker,
        LvProgressBar: LvProgressBar,
        Editor,
        VsToast,
    },
    setup() {
        const active = ref(0);
        const loading = ref(false);
        const page = usePage();

        const user = computed(() => page.props.auth.user);

        const termsAndCondition = ref([]);
        const data = new FormData();
        const errors = ref(null);
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
        const business_permit_image_src = ref("");
        const sk = ref("");
        const pk = ref("");
        const bank_name = ref("");
        const account_name = ref("");
        const account_number = ref("");
        const terms = ref("");

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
            }, 750);
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

                console.log(business_permit_image_src.value);
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
                    data.append("terms", terms.value);
                    data.append(
                        "business_permit_image_src",
                        business_permit_image_src.value
                    );

                    // Rooms Table
                    data.append("rooms", JSON.stringify(rooms.value));

                    data.append(
                        "commonAreas",
                        JSON.stringify(commonAreas.value)
                    );

                    // Amenities Table
                    data.append("amenities", JSON.stringify(amenities.value));

                    // Rules Table
                    data.append("short_term", short_term.value);
                    data.append("mix_gender", mix_gender.value);
                    data.append("curfew", curfew.value);
                    data.append("curfew_hours", curfew_hours.value);
                    data.append("minimum_stay", minimum_stay.value);
                    data.append("rules", JSON.stringify(rules.value));

                    data.append("pk", pk.value);
                    data.append("sk", sk.value);
                    data.append("bank_name", bank_name.value);
                    data.append("account_number", account_number.value);
                    data.append("account_name", account_name.value);

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
                                router.get(route("owner.dashboard"));
                            }, 1500);
                        })
                        .catch((error) => {
                            errors.value = error.response.data.errors;
                            console.log(data);
                            console.log(error);
                            loading.value = false;
                        });
                }
            );
        };

        const form = useForm({
            first_name: user.first_name,
            middle_name: user.middle_name,
            last_name: user.last_name,
            phone_number: user.phone_number,
            email: user.email,
            image: user.image,
            bio: user.bio,
            pk: user.pk,
            sk: user.sk,
            bank_name: user.bank_name,
            account_name: user.account_name,
            account_number: user.account_number,
            payment_settings: true,
        });
        const openTermsModal = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "block";
        };

        const closeTermsModal = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "none";
        };

        const logOut = () => {
            axios
                .post(route("logout"), {})
                .then((response) => {
                    location.reload();
                })
                .catch((error) => {
                    errors.value = error.response.data.errors;
                });
        };

        const errorMessages = ref({
            property_name: "",
            description: "",
            floors_total: "",
            rooms_total: "",
            business_permit_image_src: "",
            dorm_image_src: "",
            address: "",
            short_term: "",
            mix_gender: "",
            curfew_hours: "",
            detailed_address: "",
            terms: "",
            sk:"",
            pk:"",
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
                case 8:
                    isValid = validatePM();
                    break;
                case 9:
                    isValid = validateTerms();
                    break;
                default:
                    (isValid = true), (errors = {});
            }
            return isValid;
        };

        const validatePM = () => {
            let isValid = true;
            errorMessages.value.pk = "";
            errorMessages.value.sk = "";

            if (pk.value.trim() === "") {
                isValid = false;
                errorMessages.value.pk =
                    "Paymongo Public key is requred";
            }
            if (sk.value.trim() === "") {
                isValid = false;
                errorMessages.value.sk =
                    "Paymongo Secret key is requred";
            }

            return isValid;
        };

        const validateTerms = () => {
            let isValid = true;
            errorMessages.value.terms = "";

            if (terms.value.trim() === "") {
                isValid = false;
                errorMessages.value.terms =
                    "Please add your dorm terms & condition";
            }

            return isValid;
        };

        const validateRules = () => {
            let isValid = true;
            errorMessages.value.short_term = "";
            errorMessages.value.mix_gender = "";
            errorMessages.value.curfew_hours = "";

            if (short_term.value.trim() === "") {
                isValid = false;
                errorMessages.value.short_term = "Select minimum stay.";
            }

            if (mix_gender.value.trim() === "") {
                isValid = false;
                errorMessages.value.mix_gender = "Select mix gender.";
            }

            if (curfew.value.trim() === "") {
                isValid = false;
                errorMessages.value.curfew = "Select curfew hours.";
            }

            return isValid;
        };

        const validateAmenities = () => {
            let isValid = true;
            const minAmenities = 2;
            errorMessages.value.amenities = "";

            if (amenities.value.length <= minAmenities) {
                isValid = false;
                VsToast.show({
                    title: "Warning",
                    message: "Please select more than 2 amenities",
                    variant: "warning",
                });
            }
            console.log(parseInt(amenities.value.length));
            return isValid;
        };

        const validateAddress = () => {
            let isValid = true;
            errorMessages.value.address = "";
            errorMessages.value.detailed_address = "";

            if (address.value.trim() === "") {
                isValid = false;
                errorMessages.value.address = "Map Address is required.";
            }

            if (detailed_address.value.trim() === "") {
                isValid = false;
                errorMessages.value.detailed_address =
                    "Detailed address is required.";
            }

            return isValid;
        };

        const validateRoomsTotal = () => {
            const totalRooms = parseInt(rooms_total.value);

            if (rooms.value.length !== totalRooms) {
                VsToast.show({
                    title: "Warning",
                    message: "Please add exactly " + totalRooms + " rooms.",
                    variant: "warning",
                });
                console.error("Please add exactly " + totalRooms + " rooms.");
                return false;
            }

            return true;
        };

        const validateBusinessPermit = () => {
            let isValid = true;
            errorMessages.value.business_permit_image_src = "";

            if (business_permit_image_src.value === null) {
                isValid = false;
                errorMessages.value.business_permit_image_src =
                    "Image is required.";
            }
            return isValid;
        };

        const validateDormImage = () => {
            let isValid = true;
            errorMessages.value.dorm_image_src = "";

            if (dorm_image_src.value === null) {
                isValid = false;
                errorMessages.value.dorm_image_src = "Image is required.";
            }
            return isValid;
        };

        const validateDormDetails = () => {
            let isValid = true;
            errorMessages.value.property_name = "";
            errorMessages.value.description = "";
            errorMessages.value.floors_total = "";
            errorMessages.value.rooms_total = "";
            errorMessages.value.commonArea = [];

            if (property_name.value.trim() === "") {
                isValid = false;
                errorMessages.value.property_name = "Dorm Name is required.";
            }

            if (description.value.trim() === "") {
                isValid = false;
                errorMessages.value.description = "Description is required.";
            }

            if (floors_total.value === "") {
                isValid = false;
                errorMessages.value.floors_total =
                    "Number of floors is required.";
            }

            if (rooms_total.value == null || rooms_total.value === "") {
                isValid = false;
                errorMessages.value.rooms_total =
                    "Number of rooms is required.";
            }
            if (active.value === 1) {
                errorMessages.value.commonArea = [];

                commonAreas.value.forEach((commonArea, index) => {
                    // Check if commonArea is defined
                    if (commonArea) {
                        // Validate if there's data for both description and image
                        const hasDescription =
                            commonArea.name && commonArea.name.trim() !== "";
                        const hasImage = commonArea.image !== null;

                        // Initialize error messages for the current common area
                        errorMessages.value.commonArea[index] = {
                            name: "",
                            src: "",
                            // Add other properties if needed
                        };

                        if (!hasDescription) {
                            isValid = false;
                            // Set error message for the current common area
                            errorMessages.value.commonArea[index].name =
                                "Description is required.";
                        }

                        if (!hasImage) {
                            isValid = false;
                            // Set error message for the current common area
                            errorMessages.value.commonArea[index].src =
                                "Image is required.";
                        }
                    }
                });
            }
            return isValid;
        };

        const validateRooms = () => {
            let isValid = true;
            errorMessages.value.room = [];

            if (active.value === 2) {
                errorMessages.value.room = [];
                rooms.value.forEach((room, index) => {
                    if (room) {
                        const hasName = room.name && room.name.trim() !== "";
                        const hasFee = room.fee !== null;
                        const hasAdvance = room.advance !== null;
                        const hasDeposit = room.deposit !== null;
                        const hasTypeOfRoom =
                            room.type_of_room &&
                            room.type_of_room.trim() !== "";
                        const hasAircon =
                            room.is_aircon && room.is_aircon.trim() !== "";
                        const hasFurniture =
                            room.furnished_type &&
                            room.furnished_type.trim() !== "";
                        const hasImage = room.src !== null;

                        errorMessages.value.room[index] = {
                            name: "",
                            src: "",
                            fee: "",
                            advance: "",
                            deposit: "",
                            type_of_room: "",
                            is_aircon: "",
                            furnished_type: "",
                        };

                        if (!hasImage) {
                            isValid = false;
                            errorMessages.value.room[index].src =
                                "Image is required.";
                        }

                        if (!hasFee) {
                            isValid = false;
                            errorMessages.value.room[index].fee =
                                "Monthly fee is required.";
                        }

                        if (!hasAdvance) {
                            isValid = false;
                            errorMessages.value.room[index].advance =
                                "Advance fee is required.";
                        }

                        if (!hasDeposit) {
                            isValid = false;
                            errorMessages.value.room[index].deposit =
                                "Deposit fee is required.";
                        }

                        if (!hasTypeOfRoom) {
                            isValid = false;
                            errorMessages.value.room[index].type_of_room =
                                "Please select the capacity.";
                        }

                        if (!hasAircon) {
                            isValid = false;
                            errorMessages.value.room[index].is_aircon =
                                "Select aircondition or non-aircondition";
                        }

                        if (!hasFurniture) {
                            isValid = false;
                            errorMessages.value.room[index].furnished_type =
                                "Select funished type";
                        }

                        if (!hasName) {
                            isValid = false;
                            errorMessages.value.room[index].name =
                                "Room Name is required.";
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
            handleNext,
            form,
            active,
            loading,
            termsAndCondition,
            terms,
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
            progress,
            errors,
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
            openTermsModal,
            closeTermsModal,
            logOut,
            pk,
            sk,
            bank_name,
            account_name,
            account_number,
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
    <nav
        class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
    >
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center px-10 justify-between">
                <a href="/">
                    <ApplicationLogo />
                </a>

                <div class="flex items-center">
                    <div class="flex items-center ml-3">
                        <AppDropdown>
                            <button
                                type="button"
                                class="flex text-sm text-black dark:text-white hover:text-gray-400"
                                aria-expanded="false"
                            >
                                {{ $page.props.auth.user.first_name }}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="ml-1 h-5 w-5 fill-current"
                                >
                                    <path
                                        d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"
                                    ></path>
                                </svg>
                            </button>

                            <AppDropdownContent class="">
                                <AppDropdownItem
                                    @click="logOut()"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                    role="menuitem"
                                >
                                    Logout
                                </AppDropdownItem>
                            </AppDropdownContent>
                        </AppDropdown>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-[2520px] mt-16 xl:px-20 md:px-10 sm:px-2 px-4">
        <div class="max-w-screen-lg mx-auto">
            <div
                class="w-full flex justify-center items-center"
                v-if="active == 0"
            >
                <div class="" style="height: 300px; width: 300px">
                    <img
                        src="/images/logo.png"
                        alt="logo"
                        style="width: 100%"
                        class="mt-10"
                    />
                    <p class="text-2xl text-center mt-5 font-fold">
                        Welcome to Dormhub!
                    </p>

                    <p class="text-md text-center mt-5 font-fold">
                        Register your dorm free by answering the following
                        questions.
                    </p>
                </div>
            </div>
            <div v-if="active == 9">
                <div>
                    <div class="text-md text-black mb-4">
                        Terms & Condition:
                    </div>
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
                    <span class="text-xs text-red-500 ml-2"
                        >{{ errorMessages.terms }}
                    </span>
                </div>
                <div class="w-full mt-2">
                    <input
                        type="checkbox"
                        value="I guarantee that all details provided are accurate and true."
                        v-model="termsAndCondition"
                    />
                    <label class="ml-2 text-sn"
                        >* I guarantee that all details provided are accurate
                        and true.</label
                    >
                </div>

                <div class="w-full mt-2">
                    <input
                        type="checkbox"
                        value="I agree with the terms and condition."
                        v-model="termsAndCondition"
                    />
                    <label class="ml-2 text-sm"
                        >* I agree with the
                        <span
                            class="text-orange-400 cursor-pointer hover:underline"
                            >terms and condition</span
                        ></label
                    >
                </div>
            </div>
            <!--BusinessPermit-->
            <div class="w-full" v-if="active == 3">
                <p class="text-2xl font-bold mt-1 ml-2">Business Permit</p>

                <p class="text-xs mt-1 ml-2">Upload your business permit.</p>

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
                            :class="{
                                'border-red-500':
                                    !!errorMessages.business_permit_image_src,
                            }"
                        >
                            <img
                                v-if="business_permit_image_src"
                                :src="business_permit_image_src"
                                alt="business permit"
                                class="h-48 w-auto rounded-lg"
                            />
                            <span v-else>Input Image</span>
                        </div>
                    </label>
                    <span class="text-xs text-red-500 ml-2"
                        >{{ errorMessages.business_permit_image_src }}
                    </span>
                </div>

                <p class="text-xs text-red-500 ml-2 mt-4">
                    {{ validationError("business_permit_image", errors) }}
                </p>
            </div>
            <!--Dorm Image-->
            <div class="w-full" v-if="active == 4">
                <p class="text-2xl font-bold mt-1 ml-2">Dorm Image</p>

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

                    <label for="dorm_image" class="relative cursor-pointer">
                        <div
                            class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                            :class="{
                                'border-red-500':
                                    !!errorMessages.dorm_image_src,
                            }"
                        >
                            <img
                                v-if="dorm_image_src"
                                :src="dorm_image_src"
                                alt="dorm_image"
                                class="h-48 w-auto rounded-lg"
                            />
                            <span v-else>Input Image</span>
                        </div>
                    </label>
                    <span class="text-xs text-red-500 ml-2"
                        >{{ errorMessages.dorm_image_src }}
                    </span>
                </div>

                <p class="text-xs text-red-500 ml-2 mt-4">
                    {{ validationError("business_permit_image", errors) }}
                </p>
            </div>
            <!--Address-->
            <div class="flex flex-col w-full" v-if="active == 5">
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
                    <span class="text-xs text-red-500 ml-2"
                        >{{ errorMessages.address }}
                    </span>
                </div>

                <div class="w-full p-2">
                    <InputLabel
                        class="text-black"
                        for="detailed_address"
                        value="Detailed Address"
                    />

                    <TextInput
                        id="detailed_address"
                        type="text"
                        class="mt-1 block w-full text-black"
                        v-model="detailed_address"
                        :class="{
                            'border-red-500': !!errorMessages.detailed_address,
                        }"
                        required
                        autocomplete="detailed_address"
                        placeholder="House No., Street, Barangay, Municipaluty, Province, Region"
                    />
                    <span class="text-xs text-red-500 ml-2"
                        >{{ errorMessages.detailed_address }}
                    </span>
                </div>

                <div class="w-full p-2">
                    <MapboxMap
                        style="height: 310px"
                        access-token="pk.eyJ1IjoiYmFsb2dzeHh4IiwiYSI6ImNsbHA1dDN2MDAydGczZXFqZHprcW44dXIifQ.Z0dcyAB1W1B4-jcaqC_NKA"
                        map-style="mapbox://styles/mapbox/streets-v11"
                        :center="[long, lat]"
                        :zoom="15"
                    >
                        <MapboxMarker
                            :lng-lat="[long, lat]"
                            :draggable="true"
                            @mb-dragend="mapDrag($event)"
                        />
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
                        <InputLabel
                            class="text-black"
                            for="property_name"
                            value="Dorm Name"
                        />

                        <TextInput
                            id="property_name"
                            type="text"
                            class="mt-1 block w-full text-black"
                            v-model="property_name"
                            :class="{
                                'border-red-500': !!errorMessages.property_name,
                            }"
                            required
                            autocomplete="name"
                            placeholder="Dorm Name"
                        />
                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.property_name }}
                        </span>
                    </div>

                    <div class="w-full mx-1">
                        <InputLabel
                            class="text-black"
                            for="description"
                            value="Describe your dorm ?"
                        />

                        <TextInput
                            id="description"
                            type="text"
                            class="mt-1 block w-full text-black"
                            v-model="description"
                            :class="{
                                'border-red-500': !!errorMessages.description,
                            }"
                            required
                            autocomplete="description"
                            placeholder="Describe your dorm ?"
                        />

                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.description }}
                        </span>
                    </div>
                </div>

                <div class="w-full px-2 flex flex-row">
                    <div class="w-full mx-1">
                        <InputLabel
                            for="floors_total"
                            class="text-black"
                            value="How many floors in total ?"
                        />

                        <TextInput
                            id="floors_total"
                            type="number"
                            class="mt-1 block w-full text-black"
                            v-model="floors_total"
                            :class="{
                                'border-red-500': !!errorMessages.floors_total,
                            }"
                            required
                            autocomplete="floors_total"
                            placeholder="No. of Floors"
                        />

                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.floors_total }}
                        </span>
                    </div>

                    <div class="w-full mx-1">
                        <InputLabel
                            class="text-black"
                            for="rooms_total"
                            value="How many rooms in total ?"
                        />

                        <TextInput
                            id="rooms_total"
                            type="number"
                            class="mt-1 block w-full text-black"
                            v-model="rooms_total"
                            required
                            :class="{
                                'border-red-500': !!errorMessages.rooms_total,
                            }"
                            autocomplete="rooms_total"
                            placeholder="No. of Rooms"
                        />

                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.rooms_total }}
                        </span>
                    </div>
                </div>
                <div class="w-full mt-2">
                    <button
                        class="py-2 px-4 bg-orange-400 ml-3 mt-1 rounded-md text-white"
                        @click="addCommonAreas()"
                    >
                        Add Common Areas
                    </button>
                </div>
                <div class="w-full px-3">
                    <div
                        class="w-ful flex flex-col p-3 mt-2 border rounded-xl border-gray-400"
                        v-for="(commonArea, index) in commonAreas"
                        :key="index"
                    >
                        <div class="w-full">
                            <span
                                class="float-right cursor-pointer"
                                @click="removeCommonAreas(index)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </span>
                            <InputLabel
                                class="text-black"
                                for="decription_Areas"
                                value="Description:"
                            />

                            <TextInput
                                id="decription_Areas"
                                type="text"
                                class="mt-1 block w-full text-black"
                                v-model="commonArea.name"
                                :class="{
                                    'border-red-500':
                                        !!errorMessages.commonArea[index]
                                            ?.name && !commonArea.name,
                                }"
                                required
                                placeholder="e.g Living Room"
                            />
                            <div v-if="errorMessages.commonArea[index]">
                                <p class="text-xs text-red-500 ml-2">
                                    {{ errorMessages.commonArea[index].name }}
                                </p>
                            </div>
                        </div>

                        <InputLabel
                            value="Common Areas Image"
                            class="text-black"
                        />

                        <input
                            type="file"
                            :id="'areas_image' + index"
                            :ref="'areas_image_' + index"
                            style="display: none"
                            @change="
                                roomAreasChange(
                                    $event,
                                    'areas_image' + index,
                                    index
                                )
                            "
                            accept="image/*"
                        />

                        <label
                            :for="'areas_image' + index"
                            class="relative cursor-pointer"
                        >
                            <div
                                class="h-80 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                :class="{
                                    'border-red-500':
                                        !!errorMessages.commonArea[index]
                                            ?.src && !commonArea.src,
                                }"
                            >
                                <img
                                    v-if="commonArea.src"
                                    :src="commonArea.src"
                                    alt="areas_image"
                                    class="h-80 w-full rounded-lg object-cover bg-no-repeat bg-center"
                                    @click="
                                        roomAreasClick('areas_image' + index)
                                    "
                                />
                                <span v-else
                                    >Click to Input Common Areas Image</span
                                >
                            </div>
                        </label>
                        <div v-if="errorMessages.commonArea[index]">
                            <p class="text-xs text-red-500 ml-2">
                                {{ errorMessages.commonArea[index].src }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!--Rooms-->
            <div class="w-full" v-if="active == 2">
                <p class="text-2xl font-bold mt-1 ml-2 mb-2">
                    Step 2: Add rooms
                </p>
                <p class="text-sm text-gray-600 font-bold ml-2 mb-2">
                    The total number of rooms you have is
                    {{ rooms_total }} rooms.
                </p>
                <div class="w-full px-3">
                    <div
                        class="w-ful flex flex-col p-3 mt-2 border rounded-xl border-gray-400"
                        v-for="(room, index) in rooms"
                        :key="index"
                    >
                        <div class="w-full">
                            <span
                                class="float-right cursor-pointer"
                                @click="removeRoom(index)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </span>
                        </div>

                        <InputLabel value="Room Image" class="text-black" />

                        <input
                            type="file"
                            :id="'room_image' + index"
                            :ref="'room_image_' + index"
                            style="display: none"
                            @change="
                                roomImageChange(
                                    $event,
                                    'room_image' + index,
                                    index
                                )
                            "
                            accept="image/*"
                            required
                        />

                        <label
                            :for="'room_image' + index"
                            class="relative cursor-pointer"
                        >
                            <div
                                class="h-80 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                :class="{
                                    'border-red-500':
                                        !!errorMessages.room[index]?.src &&
                                        !room.src,
                                }"
                            >
                                <img
                                    v-if="room.src"
                                    :src="room.src"
                                    alt="room_image"
                                    class="h-80 w-full rounded-lg object-cover bg-no-repeat bg-center"
                                    @click="
                                        roomImageClick('room_image' + index)
                                    "
                                />
                                <span v-else>Click to Input Room Image</span>
                            </div>
                        </label>
                        <div v-if="errorMessages.room[index]">
                            <p class="text-xs text-red-500 ml-2">
                                {{ errorMessages.room[index].src }}
                            </p>
                        </div>

                        <div class="w-full mt-3">
                            <div class="flex flex-row mt-3">
                                <div class="w-full mx-1">
                                    <InputLabel
                                        class="text-black"
                                        for="name"
                                        value="Room Name"
                                    />

                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="block w-full text-black"
                                        v-model="room.name"
                                        required
                                        placeholder="Room Name"
                                    />
                                    <div v-if="errorMessages.room[index]">
                                        <p class="text-xs text-red-500 ml-2">
                                            {{ errorMessages.room[index].name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="w-full mx-1">
                                    <InputLabel
                                        class="text-black"
                                        for="type_of_room"
                                        value="Capacity"
                                    />

                                    <select
                                        required
                                        v-model="room.type_of_room"
                                        class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="Room for 1">
                                            Room for 1
                                        </option>
                                        <option value="Room for 2">
                                            Room for 2
                                        </option>
                                        <option value="Room for 3">
                                            Room for 3
                                        </option>
                                        <option value="Room for 4">
                                            Room for 4
                                        </option>
                                        <option value="Room for 5">
                                            Room for 5
                                        </option>
                                    </select>

                                    <div v-if="errorMessages.room[index]">
                                        <p class="text-xs text-red-500 ml-2">
                                            {{
                                                errorMessages.room[index]
                                                    .type_of_room
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row mt-3">
                                <div class="w-full mx-1">
                                    <InputLabel
                                        class="text-black"
                                        or="furnished_type"
                                        value="Fursnished Type"
                                    />

                                    <select
                                        required
                                        v-model="room.furnished_type"
                                        class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="Furnished">
                                            Furnished
                                        </option>
                                        <option value="Semifurnised">
                                            Semifurnised
                                        </option>
                                        <option value="Bare">Bare</option>
                                    </select>
                                    <div v-if="errorMessages.room[index]">
                                        <p class="text-xs text-red-500 ml-2">
                                            {{
                                                errorMessages.room[index]
                                                    .furnished_type
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div class="w-full mx-1">
                                    <InputLabel
                                        for="is_aircon"
                                        class="text-black"
                                        value="Aircon"
                                    />

                                    <select
                                        required
                                        v-model="room.is_aircon"
                                        class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>

                                    <div v-if="errorMessages.room[index]">
                                        <p class="text-xs text-red-500 ml-2">
                                            {{
                                                errorMessages.room[index]
                                                    .is_aircon
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex flex-row mt-3">
                            <div class="w-full mx-1">
                                <InputLabel
                                    for="fee"
                                    class="text-black"
                                    value="Monthly Fee"
                                />

                                <TextInput
                                    id="fee"
                                    type="number"
                                    class="mt-1 block w-full text-black"
                                    placeholder="0"
                                    v-model="room.fee"
                                    required
                                />
                                <div v-if="errorMessages.room[index]">
                                    <p class="text-xs text-red-500 ml-2">
                                        {{ errorMessages.room[index].fee }}
                                    </p>
                                </div>
                            </div>

                            <div class="w-full mx-1">
                                <InputLabel
                                    for="deposit"
                                    class="text-black"
                                    value="Deposit Fee"
                                />

                                <TextInput
                                    id="deposit"
                                    type="number"
                                    placeholder="0"
                                    class="mt-1 block w-full text-black"
                                    v-model="room.deposit"
                                    required
                                />
                                <div v-if="errorMessages.room[index]">
                                    <p class="text-xs text-red-500 ml-2">
                                        {{ errorMessages.room[index].deposit }}
                                    </p>
                                </div>
                            </div>

                            <div class="w-full mx-1">
                                <InputLabel
                                    for="advance"
                                    class="text-black"
                                    value="Advance Fee"
                                />

                                <TextInput
                                    id="advance"
                                    type="number"
                                    placeholder="0"
                                    class="mt-1 block w-full text-black"
                                    v-model="room.advance"
                                    required
                                />
                                <div v-if="errorMessages.room[index]">
                                    <p class="text-xs text-red-500 ml-2">
                                        {{ errorMessages.room[index].advance }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full mt-2">
                    <button
                        class="py-2 px-4 bg-orange-400 ml-3 mt-1 rounded-md text-white"
                        @click="addRoom()"
                    >
                        Add Room
                    </button>
                </div>
            </div>
            <!--Rules-->
            <div class="w-full" v-if="active == 6">
                <p class="text-2xl font-bold mt-1 ml-2 text-black">Rules</p>

                <p class="text-xs mt-1 ml-2 text-black">
                    Your house your rules. Define the dos and dont of your
                    tenants.
                </p>

                <div class="w-full flex flex-col gap-3 mt-10">
                    <div class="w-full px-1">
                        <InputLabel
                            class="text-black"
                            value="Is short-term stayed allowed ?"
                        />

                        <select
                            v-model="short_term"
                            class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>

                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.short_term }}
                        </span>
                        <div class="my-4" v-if="short_term == 'Yes'">
                            <InputLabel
                                class="text-black"
                                value="What is the minimum stayed allowed ?"
                            />

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
                        <div class="my-4 hidden" v-else>
                            <InputLabel
                                class="text-black"
                                value="What is the minimum stayed allowed ?"
                            />

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
                        <InputLabel
                            class="text-black"
                            value="Is Co-ed mixed gender allowed ?"
                        />

                        <select
                            v-model="mix_gender"
                            class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>

                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.mix_gender }}
                        </span>
                    </div>

                    <div class="w-full px-1">
                        <InputLabel
                            class="text-black"
                            value="Do you have a curfew ?"
                        />

                        <select
                            v-model="curfew"
                            class="w-full border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>

                        <span class="text-xs text-red-500 ml-2"
                            >{{ errorMessages.curfew }}
                        </span>
                        <div v-if="curfew == 'Yes'">
                            <InputLabel
                                class="text-black"
                                value="What is the curfew hours ?"
                            />

                            <TextInput
                                id="curfew_hours"
                                type="text"
                                class="block w-full text-black"
                                v-model="curfew_hours"
                                required
                                autocomplete="curfew_hours"
                                placeholder="e. 12am-3am"
                            />
                            <span class="text-xs text-red-500 ml-2"
                                >{{ validationError("curfew_hours", errors) }}
                            </span>
                        </div>
                        <div class="hidden mt-5" v-else>
                            <InputLabel
                                value="What is the curfew hours ?"
                                class="text-xs"
                            />

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
                    <div
                        class="w-full flex flex-row mt-2"
                        v-for="(rule, index) in rules"
                        :key="index"
                    >
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

                        <div
                            style="width: 5%"
                            class="flex justify-center items-center mt-5"
                        >
                            <span
                                class="cursor-pointer"
                                @click="removeRule(index)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="w-full px-1">
                    <button
                        class="py-2 px-4 bg-cyan-500 mt-1 rounded-md"
                        @click="addRule()"
                    >
                        Add Rule
                    </button>
                </div>
            </div>
            <!--Amenities-->
            <div class="w-full" v-if="active == 7">
                <p class="text-2xl font-bold mt-1 ml-3">Amenities</p>

                <p class="text-xs mt-1 ml-3">Select all apply</p>

                <div class="w-full flex flex-row mt-5">
                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('WIFI')"
                            :class="{
                                'bg-gray-300': amenities.includes('WIFI'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            WIFI
                        </button>
                    </div>

                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Security')"
                            :class="{
                                'bg-gray-300': amenities.includes('Security'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Security
                        </button>
                    </div>
                </div>

                <div class="w-full flex flex-row mt-5">
                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Cleaning Service')"
                            :class="{
                                'bg-gray-300':
                                    amenities.includes('Cleaning Service'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Cleaning Service
                        </button>
                    </div>

                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Parking')"
                            :class="{
                                'bg-gray-300': amenities.includes('Parking'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Parking
                        </button>
                    </div>
                </div>

                <div class="w-full flex flex-row mt-5">
                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Mailboxes')"
                            :class="{
                                'bg-gray-300': amenities.includes('Mailboxes'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Mailboxes
                        </button>
                    </div>

                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Vending Machines')"
                            :class="{
                                'bg-gray-300':
                                    amenities.includes('Vending Machines'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Vending Machines
                        </button>
                    </div>
                </div>

                <div class="w-full flex flex-row mt-5">
                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Laundry')"
                            :class="{
                                'bg-gray-300': amenities.includes('Laundry'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Laundry
                        </button>
                    </div>

                    <div class="w-full m-2">
                        <button
                            class="py-2 px-4 rounded-sm w-full"
                            @click="addAmenities('Pet Friendly')"
                            :class="{
                                'bg-gray-300':
                                    amenities.includes('Pet Friendly'),
                            }"
                            style="border: 1px solid black; height: 60px"
                        >
                            Pet Friendly
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full" v-if="active == 8">
                <p class="text-2xl font-bold mt-1 ml-3">Payment Method Setup</p>

                <p class="text-xs mt-1 ml-3">
                    Set up your payment method to receive payments.
                </p>

                <div class="w-full">
                    <div
                        class="flex-1 pl-6 pr-6 lg:pt-6"
                        v-if="user.user_type == 'owner'"
                    >
                        <!--Paymongo-->
                        <div class="mb-6 last:mb-0">
                            <div>
                                <div>
                                    <div class="flex gap-2 items-center">
                                        <label class="block font-bold mb-2"
                                            >Paymongo</label
                                        >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            color="green"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-5 h-5 cursor-pointer hover:bg-orange-400 animate-bounce"
                                            @click="openTermsModal()"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                                            />
                                        </svg>
                                    </div>
                                    <span class="text-xs text-red-500"></span>
                                </div>
                            </div>

                            <div class="grid grid-rows-2 gap-2">
                                <!--PK-->
                                <div class="relative">
                                    <input
                                        id="pk"
                                        v-model="pk"
                                        required=""
                                        placeholder="Paymongo Public Key"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
                                            /></svg></span>
                                </div>
                                <span class="text-xs text-red-500 ml-2"
                                    >{{ errorMessages.pk }}
                                </span>
                                <!--SK-->
                                <div class="relative">
                                    <input
                                        id="sk"
                                        v-model="sk"
                                        required=""
                                        placeholder="Paymongo Secret Key"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
                                            /></svg></span>
                                </div>
                                <span class="text-xs text-red-500 ml-2"
                                    >{{ errorMessages.sk}}
                                </span>
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-slate-400 mt-1"
                            >
                                Required. Paymongo
                            </div>
                        </div>
                        <!--Bank-->
                        <div
                            class="mb-6 last:mb-0"
                            v-if="user.user_type == 'owner'"
                        >
                            <div>
                                <div>
                                    <label class="block font-bold mb-2"
                                        >Bank Details
                                        <span class="text-sm text-gray-400"
                                            >(Optional)</span
                                        ></label
                                    >
                                </div>
                            </div>

                            <div class="grid grid-rows-2 gap-2">
                                <!--Bank Name-->
                                <div class="relative">
                                    <input
                                        id="bank_name"
                                        v-model="bank_name"
                                        placeholder="Bank Name"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"
                                            /></svg></span>
                                </div>
                                <!--Account Name-->
                                <div class="relative">
                                    <input
                                        id="acc_name"
                                        v-model="account_name"
                                        placeholder="Account Name"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
                                            /></svg></span>
                                </div>
                                <!--Account Number-->
                                <div class="relative">
                                    <input
                                        id="acc_number"
                                        v-model="account_number"
                                        placeholder="Account Number"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full md:w-[30%] 12 border bg-white pl-10"
                                    /><span
                                        class="inline-flex justify-center items-center w-10 h-12 absolute top-0 left-0 z-10 pointer-events-none text-gray-500 dark:text-slate-400"
                                        ><svg
                                            viewBox="0 0 24 24"
                                            width="16"
                                            height="16"
                                            class="inline-block"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"
                                            /></svg></span>
                                </div>
                            </div>
                            <div
                                class="text-xs text-gray-500 dark:text-slate-400 mt-1"
                            >
                                Optional. Bank Details
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Button-->
            <div
                class="items-center mt-5 space-x-2 border-gray-200 rounded-b dark:border-gray-600"
            >
                <div v-if="active == 0">
                    <button
                        data-modal-hide="defaultModal"
                        type="button"
                        @click="active = 1"
                        class="border text-black focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        Start
                    </button>
                </div>

                <div class="w-full" v-else>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active > 1"
                        @click="active > 1 ? active-- : ''"
                        type="button"
                        class="text-gray-500 bg-white mr-5 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        Back
                    </button>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active < 9"
                        @click="handleNext()"
                        type="button"
                        class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        Next
                    </button>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active == 9"
                        @click="saveDorm()"
                        type="button"
                        :disabled="loading || termsAndCondition.length < 2"
                        :class="{
                            'cursor-not-allowed':
                                loading || termsAndCondition.length < 2,
                            'bg-cyan-500 text-white':
                                !loading && termsAndCondition.length === 2,
                        }"
                        class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        {{ !!loading ? "Saving..." : "Submit" }}
                        <pulse-loader :loading="loading"></pulse-loader>
                    </button>
                </div>
            </div>
        </div>
        <div
            id="defaultModal"
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
                            <h3 class="text-xl font-semibold text-black">
                                Setup Paymongo Account
                            </h3>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                @click="closeTermsModal()"
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
                            <div class="mb-3">
                                <span class="font-bold"
                                    >1. Sign up for a Paymongo Account:</span
                                >
                                <p class="font-light">
                                    If you haven't already, go to the
                                    <a
                                        href="https://shorturl.at/klpEO"
                                        class="text-orange-400 underline cursor-pointer hover:text-opacity-25"
                                        >Paymongo website</a
                                    >
                                    and sign up for an account. You'll need a
                                    Paymongo account to access your API keys.
                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="font-bold"
                                    >2. Log in to Your Paymongo Dashboard:</span
                                >
                                <p class="font-light">
                                    After creating an account, log in to your
                                    Paymongo dashboard.
                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="font-bold"
                                    >3. Navigate to API Keys:</span
                                >
                                <p class="font-light">
                                    Once you're logged in, you can find your API
                                    keys in the dashboard. On the left sidebar,
                                    click on "Settings," and then select "API
                                    Keys."
                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="font-bold"
                                    >4. Generate API Keys:</span
                                >
                                <p class="font-light">
                                    In the API Keys section, you'll see an
                                    option to generate your keys. Click on
                                    "Create a key." You'll typically need two
                                    types of keys: a Secret Key (SK) for
                                    server-side operations and a Publishable Key
                                    (PK) for client-side operations.
                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="font-bold"
                                    >5. Copy and Save Your Keys:</span
                                >
                                <p class="font-light">
                                    After generating your keys, Paymongo will
                                    display them on the dashboard. Make sure to
                                    copy them and store them securely.
                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="font-bold"
                                    >6. Paste Your Keys:</span
                                >
                                <p class="font-light">
                                    After copying your keys, input them on the
                                    input field, PK for publik keys, SK for
                                    secret key
                                </p>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                        >
                            <button
                                @click="closeTermsModal()"
                                type="button"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            >
                                Understood
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
