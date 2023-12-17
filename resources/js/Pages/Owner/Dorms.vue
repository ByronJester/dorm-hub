<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
// import AuthenticatedLayout from '@/Layouts/ResponsiveLayout.vue';
import { usePage, useForm, Link } from "@inertiajs/vue3";
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
import VsToast from "@vuesimple/vs-toast";
import MultiSelect from 'primevue/multiselect';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

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
        Link,
        MultiSelect,
        ConfirmDialog,
        Toast
    },
    setup() {
        const page = usePage();

        const user = computed(() => page.props.auth.user);
        const confirm = useConfirm();
        const toast = useToast();

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
        const services = ref([]);
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
        const reservation = ref("");
        const termsAndCondition = ref([]);
        const landmark = ref("");
        const note = ref("");

        const isMobileView = ref(false);

        isMobileView.value = screen.width < 600;

        const service = ref([
            { name: 'Service1'},
            { name: 'Service2' },
            { name: 'Service3'},
            { name: 'Service4' },
            { name: 'Service5'}
        ]);

        const amenity = ref([
            { name: 'WIFI'},
            { name: 'Laundry'},
            { name: 'Parking'},
            { name: 'Kitchen'}
        ]);
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
                    title: "Warning",
                    message:
                        "You can only add exactly " +
                        totalRooms +
                        " rooms. Please change total of rooms if you want to add more rooms",
                    variant: "warning",
                });
                console.error("Please add exactly " + totalRooms + " rooms.");
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
                    title: "Warning",
                    message: "You can only add up to 5 common areas.",
                    variant: "warning",
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
            reservation.value = dorm.reservation;
            landmark.value = dorm.landmark;
            note.value = dorm.note;
            amenities.value = dorm.amenities.map((x) => {
                return {
                    name: x.amenity,
                };
            });
            services.value = dorm.services.map((x) => {
                return {
                    name: x.service,
                };
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
            if (validateTerms()) {
                confirm.require({
                message: 'Are you sure you want to decline this user?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                        loading.value = true;
                        const selectedServices = services.value.map(service => service.name);
                        const selectedAmenities = amenities.value.map(amenity => amenity.name);
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
                        data.append("reservation", reservation.value);
                        data.append(
                            "business_permit_image_src",
                            business_permit_image_src.value
                        );
                        data.append(
                            "landmark",
                            landmark.value
                        );
                        data.append(
                            "note",
                            note.value
                        );

                        // Rooms Table
                        data.append("rooms", JSON.stringify(rooms.value));

                        data.append(
                            "commonAreas",
                            JSON.stringify(commonAreas.value)
                        );

                        // Amenities Table
                        data.append("amenities", JSON.stringify(selectedAmenities));
                        console.log(selectedAmenities)
                        // Services Table
                        data.append("services", JSON.stringify(selectedServices));
                        console.log(selectedServices)

                        // Rules Table
                        data.append("short_term", short_term.value);
                        data.append("mix_gender", mix_gender.value);
                        data.append("curfew", curfew.value);
                        data.append("curfew_hours", curfew_hours.value);
                        data.append("minimum_stay", minimum_stay.value);
                        data.append("rules", JSON.stringify(rules.value));

                        const apiUrl = !!id.value
                            ? route("update.dorm", id.value)
                            : route("dorm.plus");

                        axios
                            .post(apiUrl, data)
                            .then((response) => {
                                loading.value = false;
                                toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully registered dorm', life: 3000 });

                                setTimeout(function () {
                                    location.reload();
                                }, 1500);
                            })
                            .catch((error) => {
                                // errors.value = error.response.data.errors;
                                loading.value = false;
                            });
                    },
                    reject: () =>{

                    }
                });
            } else {
                VsToast.show({
                    title: "Warning",
                    message: "Please add terms & condition for dorm",
                    variant: "warning",
                });
            }
        };

        const dorms = ref([]);

        onMounted(() => {
            dorms.value = page.props.dorms;
        });
        const selectedStatus = ref("Approved");

        const filteredDorms = computed(() => {
            if (selectedStatus.value === "Approved") {
                return dorms.value.filter((x) => x.status === "approved");
            } else if (selectedStatus.value === "Declined") {
                return dorms.value.filter(
                    (x) => x.status != "approved" && x.status != "pending"
                );
            } else if (selectedStatus.value === "Pending") {
                return dorms.value.filter((x) => x.status === "pending");
            }
        });

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
            commonArea: [],
            room: [],
            // Add other properties for common areas if needed
        });
        //validationn
        const validateStep = () => {
            let isValid = true;
            const roomValidation = validateRooms();
            const commTotal = validateCommonTotal();

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
                    (isValid = true), (errors = {});
            }
            return isValid;
        };

        const validateCommonTotal = () => {
            if (commonAreas.value.length < 2) {
                VsToast.show({
                    title: "Warning",
                    message: "Please add atleast 2 common areas and must have bathroom and kitchen",
                    variant: "warning",
                });
                return false;
            }

            return true;
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
            errorMessages.value.landmark = "";
            errorMessages.value.note = "";

            if (address.value.trim() === "") {
                isValid = false;
                errorMessages.value.address = "Map Address is required.";
            }

            if (detailed_address.value.trim() === "") {
                isValid = false;
                errorMessages.value.detailed_address =
                    "Detailed address is required.";
            }

            if (landmark.value.trim() === "") {
                isValid = false;
                errorMessages.value.landmark =
                    "Please input nearest landmark around your dorm.";
            }

            if (note.value.trim() === "") {
                isValid = false;
                errorMessages.value.note =
                    "Navigate seekers to locate your dorm";
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
            errorMessages.value.reservation = "";
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
            if (reservation.value == null || reservation.value === "") {
                isValid = false;
                errorMessages.value.reservation =
                    "Reservation fee is required.";
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

        const canAddDorm = ref(false);

        console.log(page.props.dorms.length);

        const checkSubscription = () => {
            const subscription = user.value.subscription;

            if (subscription == "starter" && page.props.dorms.length < 3) {
                canAddDorm.value = true;
            } else if (subscription == "basic" && page.props.dorms.length < 5) {
                canAddDorm.value = true;
            } else if (subscription == "plus") {
                canAddDorm.value = true;
            } else {
                canAddDorm.value = false;
            }
        };

        watch(() => user, checkSubscription);

        onMounted(() => {
            checkSubscription();
        });

        // Function to handle clicking "Next"
        const handleNext = () => {
            if (validateStep()) {
                // Validation passed, proceed to the next step
                active.value++;
            } else {
            }
        };

        return {
            canAddDorm,
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
            amenity,
            services,
            service,
            short_term,
            mix_gender,
            curfew,
            landmark,
            note,
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
            reservation,
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
                <span
                    class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black mr-3"
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
                <h3 class="text-3xl">Manage Dormitories</h3>
            </div>

            <hr class="h-px mt-5 bg-orange-400 border-1" />
            <div
                v-if="!canAddDorm"
                class="w-full py-4 px-6 text-white flex justify-between items-center font-bold text-sm bg-orange-400"
            >
                <p>
                    Ready to unlock even more benefits? Upgrade your
                    subscription now to create more dorm listings
                </p>
                <Link
                    class="px-3 py-1.5 rounded border border-white hover:bg-white hover:text-orange-400"
                >
                    Upgrade
                </Link>
            </div>
            <div class="flex flex-col pr-5 pt-5">
                <div class="w-full">
                    <p class="float-right">
                        <button
                            class="bg-orange-400 text-white rounded-md py-2 px-5"
                            @click="[openFormModal(), (active = 0)]"
                            :disabled="!canAddDorm"
                            :class="{
                                hidden: !canAddDorm,
                            }"
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
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                dorms.filter((x) => {
                                    return x.status == "decline";
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Declined Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50"
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
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50"
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
                mb-10
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
                <div class="relative w-full max-w-5xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow">
                        <!-- Modal header -->
                        <div
                            class="flex items-start justify-between p-4 border-b rounded-t"
                        >
                            <h3 class="text-xl font-semibold text-gray-900">
                                Add New Dorm
                            </h3>
                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
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
                            <div v-if="active == 8">
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
                                            plugins:
                                                'lists link image emoticons',
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
                                        >* I guarantee that all details provided
                                        are accurate and true.</label
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
                                <p class="text-2xl font-bold mt-1">Step 3: Business Permit</p>

                                <p class="text-xs mt-1">Upload your business permit.</p>
                                <hr class="my-5" />
                                <div class="flex flex-col items-center w-full">
                                    <input
                                        type="file"
                                        id="business_permit"
                                        class="hidden"
                                        @change="bpImageChange($event)"
                                        accept="image/*"
                                        required
                                    />
                                    <label
                                        for="business_permit"
                                        :class="{
                                            'border-red-500':
                                                !!errorMessages.business_permit_image_src,
                                        }"
                                        class="flex flex-col items-center justify-center md:w-1/2 w-full h-[500px] bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                    >
                                        <img
                                            v-if="business_permit_image_src"
                                            :src="business_permit_image_src"
                                            alt="business permit"
                                            class="h-[500px] w-full object-cover bg-no-repeat bg-center rounded-lg"
                                        />
                                        <div
                                            v-else
                                            class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                        >
                                            <span class="text-blue-500 dark:text-gray-400">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="w-8 h-8 bi bi-cloud-upload"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"
                                                    />
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"
                                                    /></svg></span>
                                            <p
                                                class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                <span class="font-semibold text-blue-500"
                                                    >Click to upload</span
                                                >
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                SVG, PNG, JPG or GIF (upto 10MB)
                                            </p>
                                        </div>
                                        <span class="text-xs text-red-500 ml-2"
                                            >{{ errorMessages.business_permit_image_src }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <!--Dorm Image-->
                            <div class="w-full" v-if="active == 4">
                                <p class="text-2xl font-bold mt-1 ml-2">Step 4: Dorm Image</p>

                                <p class="text-xs mt-1 ml-2">
                                    Allow your seeker to see your dorm
                                </p>
                                <hr class="my-5" />
                                <div class="flex flex-col items-center w-full">
                                    <input
                                        type="file"
                                        id="dorm_image"
                                        class="hidden"
                                        @change="dormImageChange($event)"
                                        accept="image/*"
                                        >
                                    <label
                                        for="dorm_image"
                                        :class="{
                                            'border-red-500': !!errorMessages.dorm_image_src,
                                        }"
                                        class="flex flex-col items-center justify-center md:w-1/2 w-full h-[500px] bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                    >
                                        <img
                                            v-if="dorm_image_src"
                                            :src="dorm_image_src"
                                            alt="dorm_image"
                                            class="h-[500px] w-full object-cover bg-no-repeat bg-center rounded-lg"
                                        />
                                        <div
                                            v-else
                                            class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                        >
                                            <span class="text-blue-500 dark:text-gray-400">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    fill="currentColor"
                                                    class="w-8 h-8 bi bi-cloud-upload"
                                                    viewBox="0 0 16 16"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"
                                                    />
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"
                                                    /></svg></span>
                                            <p
                                                class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                <span class="font-semibold text-blue-500"
                                                    >Click to upload</span
                                                >
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                SVG, PNG, JPG or GIF (upto 10MB)
                                            </p>
                                        </div>
                                        <span class="text-xs text-red-500 ml-2"
                                            >{{ errorMessages.dorm_image_src }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <!--Address-->
                            <div class="flex flex-col w-full" v-if="active == 5">
                            <p class="font-bold text-2xl">Step 5: Dorm Address</p>
                            <p class="font-bold text-sm">
                                Allow seekers to locate your dorm
                            </p>
                            <hr class="mt-5" />
                            <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="w-full md:w-9/12">
                                    <div class="flex flex-wrap -m-3">
                                        <div class="w-full p-3 md:w-1/3">
                                            <p
                                                class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                            >
                                                Map Address:
                                            </p>
                                        </div>
                                        <div class="w-full p-3 md:w-1/3">
                                            <input
                                                id="address"
                                                v-model="address"
                                                :class="{
                                                    'border-red-500':
                                                        !!errorMessages.address,
                                                }"
                                                required
                                                autocomplete="address"
                                                @input="changeAddress($event)"
                                                class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                type="text"
                                            />
                                            <span class="text-xs text-red-500 ml-2"
                                                >{{ errorMessages.address }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="w-full md:w-9/12">
                                    <div class="flex flex-wrap -m-3">
                                        <div class="w-full p-3 md:w-1/3">
                                            <p
                                                class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                            >
                                                Nearest Landmark:
                                            </p>
                                        </div>
                                        <div class="w-full p-3 md:w-1/2">
                                            <input
                                                id="detailed_address"
                                                v-model="landmark"
                                                :class="{
                                                    'border-red-500':
                                                        !!errorMessages.landmark,
                                                }"
                                                required
                                                autocomplete="landmark"
                                                placeholder="Waltermart"
                                                class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                type="text"
                                            />
                                            <span class="text-xs text-red-500 ml-2"
                                                >{{ errorMessages.landmark }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="w-full md:w-9/12">
                                    <div class="flex flex-wrap -m-3">
                                        <div class="w-full p-3 md:w-1/3">
                                            <p
                                                class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                            >
                                                Note Direction(optional):
                                            </p>
                                        </div>
                                        <div class="w-full p-3 md:w-1/2">
                                            <input
                                                id="detailed_address"
                                                v-model="note"
                                                :class="{
                                                    'border-red-500':
                                                        !!errorMessages.note,
                                                }"
                                                required
                                                autocomplete="landmark"
                                                placeholder="Kaliwa, kanan kaliwa"
                                                class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                type="text"
                                            />
                                            <span class="text-xs text-red-500 ml-2"
                                                >{{ errorMessages.note }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="w-full md:w-9/12">
                                    <div class="flex flex-wrap -m-3">
                                        <div class="w-full p-3 md:w-1/3">
                                            <p
                                                class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                            >
                                                Detailed Address:
                                            </p>
                                        </div>
                                        <div class="w-full p-3 md:w-1/2">
                                            <input
                                                id="detailed_address"
                                                v-model="detailed_address"
                                                :class="{
                                                    'border-red-500':
                                                        !!errorMessages.detailed_address,
                                                }"
                                                required
                                                autocomplete="detailed_address"
                                                placeholder="House No., Street, Barangay, Municipaluty, Province, Region"
                                                class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                type="text"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="w-full md:w-9/12">
                                    <div class="flex flex-wrap -m-3">
                                        <div class="w-full p-3 md:w-1/3">
                                            <p
                                                class="text-sm font-semibold text-gray-800 dark:text-gray-400"
                                            >
                                                Maps
                                            </p>
                                        </div>
                                        <div class="w-full p-3 md:flex-1">
                                            <div
                                                class="flex items-center justify-center w-full "
                                            >
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!--Dorm Desc-->
                            <div
                                class="flex flex-col w-full"
                                v-if="active == 1"
                            >
                                <p class="text-2xl font-bold mt-1 mb-2">
                                    Step 1: Dorm Details
                                </p>
                                <hr class="mt-5" />
                                <div
                                    class="py-6 border-b border-gray-100 dark:border-gray-800"
                                >
                                    <div class="w-full md:w-9/12">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Dorm Name:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:w-1/3">
                                                <input
                                                    id="property_name"
                                                    v-model="property_name"
                                                    :class="{
                                                        'border-red-500':
                                                            !!errorMessages.property_name,
                                                    }"
                                                    class="w-full md:w-2/3 dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                    type="text"
                                                    
                                                    placeholder="Dorm Name"
                                                />
                                                <div>
                                                    <span
                                                        class="text-xs text-red-500 ml-2"
                                                    >
                                                        {{
                                                            errorMessages.property_name
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="py-6 border-b border-gray-100 dark:border-gray-800"
                                >
                                    <div class="w-full md:w-9/12">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Describe your dorm:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:w-1/3">
                                                <input
                                                    id="description"
                                                    v-model="description"
                                                    :class="{
                                                        'border-red-500':
                                                            !!errorMessages.description,
                                                    }"
                                                    class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                    type="text"
                                                    placeholder="Dorm Description"
                                                />
                                                <span
                                                    class="text-xs text-red-500 ml-2"
                                                    >{{
                                                        errorMessages.description
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="py-6 border-b border-gray-100 dark:border-gray-800"
                                >
                                    <div class="w-full md:w-9/12">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Total no. of Floors:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:w-1/3">
                                                <input
                                                    id="floors_total"
                                                    v-model="floors_total"
                                                    :class="{
                                                        'border-red-500':
                                                            !!errorMessages.floors_total,
                                                    }"
                                                    class="w-full md:w-2/4 dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                    type="number"
                                                    min="0"
                                                    max="20"
                                                    placeholder=""
                                                />
                                                <div>
                                                    <span
                                                        class="text-xs text-red-500 ml-2"
                                                        >{{
                                                            errorMessages.floors_total
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="py-6 border-b border-gray-100 dark:border-gray-800"
                                >
                                    <div class="w-full md:w-9/12">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Total no. of Rooms:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:w-1/3">
                                                <input
                                                    id="rooms_total"
                                                    v-model="rooms_total"
                                                    required
                                                    :class="{
                                                        'border-red-500':
                                                            !!errorMessages.rooms_total,
                                                    }"
                                                    class="w-full md:w-2/4 dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                    type="number"
                                                    placeholder=""
                                                />
                                                <div>
                                                    <span
                                                        class="text-xs text-red-500 ml-2"
                                                        >{{
                                                            errorMessages.rooms_total
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="py-6 border-b border-gray-100 dark:border-gray-800"
                                >
                                    <div class="w-full md:w-9/12">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Reservation Fee:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:w-1/3">
                                                <input
                                                    id="reservation"
                                                    required
                                                    v-model="reservation"
                                                    :class="{
                                                        'border-red-500':
                                                            !!errorMessages.reservation,
                                                    }"
                                                    class="w-full md:w-2/4 dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                                    type="number"
                                                    placeholder=""
                                                />
                                                <div>
                                                    <span
                                                        class="text-xs text-red-500 ml-2"
                                                        >{{
                                                            errorMessages.reservation
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="py-6 border-b border-gray-100 dark:border-gray-800"
                                >
                                    <div class="w-full md:w-9/12">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <button
                                                    class="py-2 px-4 bg-orange-400 mt-1 rounded-md text-white"
                                                    @click="addCommonAreas()"
                                                >
                                                    Add Common Areas
                                                </button>
                                            </div>
                                            <div class="w-full md:w-1/2">
                                                <div
                                                    class="w-full flex flex-col p-3"
                                                    v-for="(
                                                        commonArea, index
                                                    ) in commonAreas"
                                                    :key="index"
                                                >
                                                    <div
                                                        class="flex flex-row gap-1 mb-2 w-full"
                                                    >
                                                        <div class="w-full">
                                                            <div
                                                                class="flex flex-row justify-between items-center"
                                                            >
                                                                <p
                                                                    class="text-md font-bold"
                                                                >
                                                                    Description:
                                                                </p>
                                                                <span
                                                                    class="float-right cursor-pointer"
                                                                    @click="
                                                                        removeCommonAreas(
                                                                            index
                                                                        )
                                                                    "
                                                                >
                                                                    <i
                                                                        class="fa-solid fa-trash-can"
                                                                    ></i>
                                                                </span>
                                                            </div>
                                                            <select
                                                                required
                                                                v-model="
                                                                    commonArea.name
                                                                "
                                                                :class="{
                                                                    'border-red-500':
                                                                        !!errorMessages
                                                                            .commonArea[
                                                                            index
                                                                        ]
                                                                            ?.name &&
                                                                        !commonArea.name,
                                                                }"
                                                                class="w-full rounded-lg border-gray-300"
                                                            >
                                                                <option>
                                                                    Living Room
                                                                </option>
                                                                <option>
                                                                    Kitchen
                                                                </option>
                                                                <option>
                                                                    Bathroom
                                                                </option>
                                                            </select>
                                                            <div
                                                                v-if="
                                                                    errorMessages
                                                                        .commonArea[
                                                                        index
                                                                    ]
                                                                "
                                                            >
                                                                <p
                                                                    class="text-xs text-red-500 ml-2"
                                                                >
                                                                    {{
                                                                        errorMessages
                                                                            .commonArea[
                                                                            index
                                                                        ].name
                                                                    }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col items-center w-full"
                                                    >
                                                        <input
                                                            required
                                                            type="file"
                                                            :id="
                                                                'areas_image' +
                                                                index
                                                            "
                                                            :ref="
                                                                'areas_image_' +
                                                                index
                                                            "
                                                            @change="
                                                                roomAreasChange(
                                                                    $event,
                                                                    'areas_image' +
                                                                        index,
                                                                    index
                                                                )
                                                            "
                                                            accept="image/*"
                                                            class="hidden"
                                                        />
                                                        <label
                                                            :for="
                                                                'areas_image' +
                                                                index
                                                            "
                                                            :class="{
                                                                'border-red-500':
                                                                    !!errorMessages
                                                                        .commonArea[
                                                                        index
                                                                    ]?.src &&
                                                                    !commonArea.src,
                                                            }"
                                                            class="flex flex-col items-center justify-center w-full h-64 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                                        >
                                                            <img
                                                                v-if="
                                                                    commonArea.src
                                                                "
                                                                :src="
                                                                    commonArea.src
                                                                "
                                                                alt="areas_image"
                                                                class="h-64 w-full rounded-lg object-cover bg-no-repeat bg-center"
                                                                @click="
                                                                    roomAreasClick(
                                                                        'areas_image' +
                                                                            index
                                                                    )
                                                                "
                                                            />
                                                            <div
                                                                v-else
                                                                class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                                            >
                                                                <span
                                                                    class="text-blue-500 dark:text-gray-400"
                                                                >
                                                                    <svg
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        width="16"
                                                                        height="16"
                                                                        fill="currentColor"
                                                                        class="w-8 h-8 bi bi-cloud-upload"
                                                                        viewBox="0 0 16 16"
                                                                    >
                                                                        <path
                                                                            fill-rule="evenodd"
                                                                            d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"
                                                                        />
                                                                        <path
                                                                            fill-rule="evenodd"
                                                                            d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"
                                                                        /></svg>
                                                                    </span>
                                                                <p
                                                                    class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                                                >
                                                                    <span
                                                                        class="font-semibold text-blue-500"
                                                                        >Click
                                                                        to
                                                                        upload</span
                                                                    >
                                                                </p>
                                                                <p
                                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                                >
                                                                    SVG, PNG,
                                                                    JPG or GIF
                                                                    (upto 10MB)
                                                                </p>
                                                            </div>
                                                            <div
                                                                v-if="
                                                                    errorMessages
                                                                        .commonArea[
                                                                        index
                                                                    ]
                                                                "
                                                            >
                                                                <p
                                                                    class="text-xs text-red-500 ml-2"
                                                                >
                                                                    {{
                                                                        errorMessages
                                                                            .commonArea[
                                                                            index
                                                                        ].src
                                                                    }}
                                                                </p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    <hr class="my-5" />
                                                </div>
                                            </div>
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
                                <div class="w-full mt-2">
                                    <button
                                        class="py-2 px-4 bg-orange-400 ml-3 mt-1 rounded-md text-white"
                                        @click="addRoom()"
                                    >
                                        Add Room
                                    </button>
                                </div>
                                <div class="w-full gap-2 grid grid-cols-1 md:grid-cols-2 px-3">
                                    <div
                                        class="w-full flex flex-col p-3 mt-2 border rounded-xl border-gray-400"
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

                                        <div class="flex flex-col items-center w-full">
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
                                                :class="{
                                                    'border-red-500':
                                                        !!errorMessages.room[index]?.src &&
                                                        !room.src,
                                                }"
                                                class="flex flex-col items-center justify-center w-full h-80 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
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
                                                <div
                                                    v-else
                                                    class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                                >
                                                    <span
                                                        class="text-blue-500 dark:text-gray-400"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="16"
                                                            height="16"
                                                            fill="currentColor"
                                                            class="w-8 h-8 bi bi-cloud-upload"
                                                            viewBox="0 0 16 16"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"
                                                            />
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"
                                                            /></svg></span>
                                                    <p
                                                        class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                                                    >
                                                        <span
                                                            class="font-semibold text-blue-500"
                                                            >Click to upload</span
                                                        >
                                                    </p>
                                                    <p
                                                        class="text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        SVG, PNG, JPG or GIF (upto 10MB)
                                                    </p>
                                                </div>
                                                <div v-if="errorMessages.room[index]">
                                                    <p class="text-xs text-red-500 ml-2">
                                                        {{ errorMessages.room[index].src }}
                                                    </p>
                                                </div>
                                            </label>
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
                                            <div class="w-full mx-1">
                                                <InputLabel
                                                    for="furnshiedDescription"
                                                    class="text-black"
                                                    value="Furnished Description"
                                                />
                                                <TextInput
                                                    id="fee"
                                                    type="text"
                                                    class="mt-1 block w-full text-black"
                                                    placeholder="ex. With bed, refridgirator etc."
                                                    required
                                                />
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
                            </div>
                            <!--Rules-->
                            <div class="w-full" v-if="active == 6">
                                <p class="text-2xl font-bold mt-1 text-black">Step 6: Rules</p>

                                <p class="text-xs mt-1 text-black">
                                    Your house your rules. Define the dos and dont of your
                                    tenants.
                                </p>
                                <hr class="mt-5" />
                                <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                    <div class="w-full md:w-9/42">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Is short term stay allowed?:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:flex-1">
                                                <select
                                                    v-model="short_term"
                                                    class="border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                >
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>

                                                <span class="text-xs text-red-500 ml-2"
                                                    >{{ errorMessages.short_term }}
                                                </span>
                                                <div class="mt-4" v-if="short_term == 'Yes'">
                                                    <InputLabel
                                                        class="text-black"
                                                        value="What is the minimum stayed allowed ?"
                                                    />

                                                    <TextInput
                                                        id="minimum_stay"
                                                        type="text"
                                                        class="mt-1 block md:w-1/3 w-full text-black"
                                                        v-model="minimum_stay"
                                                        required
                                                        autocomplete="minimum_stay"
                                                        placeholder="e.g: 3monhts/1year"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                    <div class="w-full md:w-9/14">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Is Co-ed mixed gender allowed ?:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:flex-1">
                                                <select
                                                    v-model="mix_gender"
                                                    class="border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                >
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>

                                                <span class="text-xs text-red-500 ml-2"
                                                    >{{ errorMessages.mix_gender }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                                    <div class="w-full md:w-9/14">
                                        <div class="flex flex-wrap -m-3">
                                            <div class="w-full p-3 md:w-1/3">
                                                <p
                                                    class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                                >
                                                    Do you have a curfew?:
                                                </p>
                                            </div>
                                            <div class="w-full p-3 md:flex-1">
                                                <select
                                                    v-model="curfew"
                                                    class="border-gray-300 text-black focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                >
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>

                                                <span class="text-xs text-red-500 ml-2"
                                                    >{{ errorMessages.curfew }}
                                                </span>
                                                <div class="mt-4" v-if="curfew == 'Yes'">
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
                                                        >{{
                                                            validationError(
                                                                "curfew_hours",
                                                                errors
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full grid grid-cols-4 gap-5 mt-5 px-1">
                                    <div
                                        class="w-full flex flex-col mt-2"
                                        v-for="(rule, index) in rules"
                                        :key="index"
                                    >
                                        <InputLabel class="text-black" value="Rule" />
                                        <span
                                            class="cursor-pointer"
                                            @click="removeRule(index)"
                                        >
                                            <i class="fa-solid fa-trash-can text-red-600 float-right"></i>
                                        </span>
                                        <div>
                                            <TextInput
                                                id="rule"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="rule.name"
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full px-1">
                                    <button
                                        class="py-2 px-4 mt-3 bg-orange-400 text-white rounded-md"
                                        @click="addRule()"
                                    >
                                        Add Rule
                                    </button>
                                </div>
                            </div>
                            <!--Amenities-->
                            <div class="w-full" v-if="active == 7">
                                <p class="text-2xl font-bold mt-1 ">
                                    Amenities
                                </p>

                                <p class="text-xs mt-1 ">
                                    Select all apply
                                </p>

                                
                                <div class="card flex justify-content-center border rounded mt-5">
                                        <MultiSelect v-model="amenities" :options="amenity" optionLabel="name" placeholder="Select Amenities"
                                            :maxSelectedLabels="3" class="w-full md:w-20rem" />
                                </div>
                                <div class="flex flex-row gap-5 mt-5 text-sm font-bold text-gray-800">
                                    <div v-for="(selected, index) in amenities" :key="index" class="rounded-full bg-orange-400 px-5 py-2 text-white">
                                        {{  selected.name }}
                                    </div>
                                </div>
                                <hr class="my-5" />
                                <p class="text-2xl font-bold ">
                                    Services
                                </p>

                                <p class="text-xs mt-1 ">
                                    Select all apply
                                </p>
                                    <div class="card flex justify-content-center border rounded mt-5">
                                        <MultiSelect v-model="services" :options="service" optionLabel="name" placeholder="Select Services"
                                            :maxSelectedLabels="1" class="w-full md:w-20rem" />
                                    </div>
                                    <div class="flex flex-row gap-5 mt-5 text-sm font-bold text-gray-800">
                                    <div v-for="(selected, index) in services" :key="index" class="rounded-full bg-orange-400 px-5 py-2 text-white">
                                        {{  selected.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b"
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
                                    :disabled="
                                        loading || termsAndCondition.length < 2
                                    "
                                    :class="{
                                        'cursor-not-allowed':
                                            loading ||
                                            termsAndCondition.length < 2,
                                        'bg-orange-400 text-white hover:text-gray-700 hover:bg-orange-300':
                                            !loading &&
                                            termsAndCondition.length === 2,
                                    }"
                                    class="text-gray-500 float-right focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-orange-400 focus:z-10"
                                >
                                    <ConfirmDialog />
                                    <Toast />
                                    {{ !!loading ? "Saving..." : "Submit" }}
                                    <pulse-loader
                                        :loading="loading"
                                    ></pulse-loader>
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
