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
import VsToast from "@vuesimple/vs-toast";
import Image from 'primevue/image';
import MultiSelect from 'primevue/multiselect';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

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
        Image,
        MultiSelect,
        ConfirmDialog,
        Toast
    },
    setup() {
        const active = ref(1);
        const loading = ref(false);
        const page = usePage();
        const confirm = useConfirm();
        const toast = useToast();

        const user = page.props.auth.user;

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
        const short_term = ref("No");
        const mix_gender = ref("No");
        const curfew = ref("No");
        const curfew_hours = ref("");
        const minimum_stay = ref("");
        const rules = ref([]);
        const reservation = ref("");
        const landmark = ref("");
        const note = ref("");
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
        const amenities = ref([]);
        const services = ref([]);
        const service = ref([
            { name: 'Laundry Services' },
            { name: 'Maintenance'},
            { name: 'Cleaning Services' },
            { name: 'Security Services'}
        ]);

        const amenity = ref([
            { name: 'WIFI'},
            { name: 'Laundry Area'},
            { name: 'Parking Area'},
            { name: 'Kitchen Area'}
        ]);




        const selectedPayment = ref('card'); // Default selected payment method

        const paymentMethods = [
            { label: 'Credit/Debit Card', value: 'card', logo: '/images/credicardlogo.png' },
            { label: 'Gcash', value: 'gcash', logo: '/images/gcashlogo.png' },
            { label: 'Paymaya', value: 'paymaya', logo: '/images/paymayalogo.png' },
            { label: 'GrabPay', value: 'grabpay', logo: '/images/grablogo.png' },
        ];

        // Watch for changes in the selected payment method
        watch(selectedPayment, (newValue) => {
            console.log(`Selected payment method changed to: ${newValue}`);
            // You can perform additional actions based on the selected payment method
        });

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const subscriptions = [
            { label: 'Starter', value: 'starter', description: '3 Dorm Listings', price: 300},
            { label: 'Basic', value: 'basic', description: '5 Dorm Listings', price: 500},
            { label: 'Plus', value: 'plus', description: 'Unlimited Dorm Listings', price: 1000},
        ];

        const selectedSubscription = ref(subscriptions[0]);

            // Watch for changes in the selected subscription
        watch(() => selectedSubscription.value, (newValue) => {
                console.log(`Selected subscription changed to: ${newValue.label}`);
            });

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
                    address.value = data.addresses[0].address.freeformAddress;
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

        const idPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                console.log(e);
                form.id_picture = e.target.result;
            };
        };

        //Dinagdag ko


        const SelfieidPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                console.log(e);
                form.selfie_id_picture = e.target.result;
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
                    furnished_desc: null,
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



        const saveDorm = () => {
            confirm.require({
                message: 'Are you sure you want to proceed?',
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
                    data.append("floors_total", floors_total.value);
                    data.append("rooms_total", rooms_total.value);
                    data.append("dorm_image", dorm_image.value);
                    data.append("terms", terms.value);
                    data.append("reservation", reservation.value);
                    data.append("landmark", landmark.value);
                    data.append("note", note.value);
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

                    data.append("pk", pk.value);
                    data.append("sk", sk.value);
                    data.append("bank_name", bank_name.value);
                    data.append("account_number", account_number.value);
                    data.append("account_name", account_name.value);

                    // Subscription Table
                    data.append("subscription", selectedSubscription.value.value);


                    axios
                        .post(route("save.dorm"), data)
                        .then((response) => {
                            loading.value = false;

                            // swal(
                            //     "Success!",
                            //     "Your dorm has been save.",
                            //     "success"
                            // );

                            // setTimeout(function () {
                            //     router.get(route("owner.dashboard"));
                            // }, 1500);
                            window.location.href = response.data
                        })
                        .catch((error) => {
                            // errors.value = error.response.data.errors;
                            // console.log(data);
                            // console.log(error);
                            // loading.value = false;
                        });
                },
                reject: ()=>{

                }
            });
        };
        const statuss = ref('pending')
        const form = useForm({
            first_name: user.first_name,
            middle_name: user.middle_name,
            last_name: user.last_name,
            phone_number: user.phone_number,
            id_picture: '',
            selfie_id_picture: '',
            status: statuss.value
        });

        const confirmSubmit = () => {
            axios
                .post(route("submit.id"), form)
                .then((response) => {
                    VsToast.show({
                        title: "Submit",
                        message: "You've Submit successfuly",
                        variant: "success",
                    });
                    location.reload();
                })
                .catch((error) => {
                    VsToast.show({
                        title: "Error",
                        message: error,
                        variant: "error",
                    });
                });
        };

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
                    // errors.value = error.response.data.errors;
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
            const commtotal = validateCommonTotal();

            switch (active.value) {
                case 1:
                    isValid = commtotal && validateDormDetails();
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
                case 10:
                    isValid
                    break;
                default:
                    (isValid = true), (errors.value = {});
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

        const validatePM = () => {
            let isValid = true;
            errorMessages.value.pk = "";
            errorMessages.value.sk = "";

            if (pk.value.trim() === "") {
                isValid = false;
                errorMessages.value.pk = "Xendit Public key is requred";
            }
            if (sk.value.trim() === "") {
                isValid = false;
                errorMessages.value.sk = "Xendit Secret key is requred";
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
                        const hasFurnishDesc = room.furnished_desc && room.furnished_desc.trim() !== "";
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
                            furnished_desc: "",
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
                                "Select furnished type";
                        }

                        if (!hasFurnishDesc) {
                            isValid = false;
                            errorMessages.value.room[index].furnished_desc =
                                "Enter the Furnished Description";
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


        const handleNext = () => {
            if (validateStep()) {
                active.value++;
            } else {
            }
        };

        const userStatus = ref(user.status)

        const getUserStatus = () => {
            if(userStatus.value == 'pending') {
                axios
                    .get(route("owner.status"))
                    .then((response) => {
                        userStatus.value = response.data
                    })
                    .catch((error) => {

                    });
            }

        }

        const recallTimer = ref(null);

        onMounted(() => {
            clearInterval(recallTimer.value);

            recallTimer.value = setInterval(() => {
                getUserStatus();
            }, 10000);
        });


        return {
            errorMessages,
            handleNext,
            paymentMethods,
            selectedSubscription,
            subscriptions,
            selectedPayment,
            moneyFormat,
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
            landmark,
            note,
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
            curfew_hours,
            minimum_stay,
            rules,
            dorm_image,
            reservation,
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
            idPictureChange,
            SelfieidPictureChange,
            saveDorm,
            confirmSubmit,
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
            userStatus
        };
    },
};
</script>

<template>

    <nav
        class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
    >
        <div class="w-full flex items-center justify-center p-3 text-white text-sm font-semibold bg-red-500" v-if="userStatus == 'decline'">
            Your account has been declined. Reason: {{ user.reason }}
        </div>
        <div class="w-full flex items-center justify-center p-3 text-white text-sm font-semibold bg-orange-400" v-if="userStatus == 'pending'">
            You have pending status for user verication. Please wait for the system admin to approve you!
        </div>
        <div class="max-w-[2520px] xl:px-20 md:px-10 sm:px-2 px-4">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
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
        </div>
    </nav>

    <div class="max-w-[2520px] mt-24 xl:px-20 md:px-10 sm:px-2 px-4" v-if="userStatus == null || userStatus == 'decline'">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="w-full items-center">
                <p class="font-bold text-2xl">Verification Details</p>
                <hr class="mt-5" />
                <form @submit.prevent="confirmSubmit">
                    <div
                        class="py-6 border-b border-gray-100 dark:border-gray-800"
                    >
                        <div class="w-full md:w-9/12">
                            <div class="flex flex-wrap -m-3">
                                <div class="w-full p-3 md:w-1/3">
                                    <p
                                        class="text-base font-semibold text-gray-700 dark:text-gray-400"
                                    >
                                        Name:
                                    </p>
                                </div>
                                <div class="w-full p-3 md:w-1/3">
                                    <input
                                        class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                        type="text"
                                        v-model='form.first_name'
                                        placeholder="First Name"
                                    />
                                </div>
                                <div class="w-full p-3 md:w-1/3">
                                    <input
                                        class="w-full px-4 py-2.5 dark:bg-gray-800 placeholder-gray-400 dark:border-gray-800 dark:placeholder-gray-500 dark:text-gray-400 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                        type="text"
                                        v-model='form.last_name'
                                        placeholder="Last Name"
                                    />
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
                                        Address:
                                    </p>
                                </div>
                                <div class="w-full p-3 md:w-1/2">
                                    <input
                                        class="w-full dark:bg-gray-800 dark:border-gray-800 px-4 placeholder-gray-400 dark:placeholder-gray-500 dark:text-gray-400 py-2.5 text-base text-gray-900 rounded-lg font-normal border border-gray-200"
                                        type="text"
                                        placeholder="Address"
                                    />
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
                                        class="text-sm font-semibold text-gray-800 dark:text-gray-400"
                                    >
                                        Verificcation ID
                                    </p>
                                    <p
                                        class="text-xs font-semibold text-orange-400"
                                    >
                                        (ex. Goverments ID, and School ID)
                                    </p>
                                </div>
                                <div class="w-full p-3 md:flex-1">
                                    <div
                                        class="flex items-center justify-center w-full md:w-1/2"
                                    >
                                    <input
                                        type="file"
                                        id="idPicture"
                                        class="hidden"
                                        @change="idPictureChange($event)"
                                        accept="image/*"
                                        required
                                    />
                                        <label
                                            for="idPicture"
                                            class="flex flex-col items-center justify-center w-full h-64 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                        >
                                            <img
                                                v-if="form.id_picture"
                                                :src="form.id_picture"
                                                alt="id picure"
                                                class="h-64 w-full object-cover bg-no-repeat bg-center rounded-lg"
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
                                                    SVG, PNG, JPG or GIF (upto
                                                    10MB)
                                                </p>
                                            </div>
                                            <input type="file" class="hidden" />
                                        </label>
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
                                        class="text-sm font-semibold text-gray-800 dark:text-gray-400"
                                    >
                                        Selfie with Verificcation ID
                                    </p>
                                    <p
                                        class="text-xs font-semibold text-orange-400"
                                    >
                                        (Upload a selfie holding verification
                                        ID)
                                    </p>
                                </div>
                                <div class="w-full p-3 md:flex-1">
                                    <div
                                        class="flex items-center justify-center w-full md:w-1/2"
                                    >
                                    <input
                                        type="file"
                                        id="selfie_id_picture"
                                        class="hidden"
                                        @change="SelfieidPictureChange($event)"
                                        accept="image/*"
                                        required
                                    />

                                        <label
                                            for="selfie_id_picture"
                                            class="flex flex-col items-center justify-center w-full h-64 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                        >
                                            <img
                                                v-if="form.selfie_id_picture"
                                                :src="form.selfie_id_picture"
                                                alt="business permit"
                                                class="h-64 w-full object-cover bg-no-repeat bg-center rounded-lg"
                                            />
                                            <div
                                                class="flex flex-col items-center justify-center px-4 pt-5 pb-6"
                                                v-else
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
                                                    or drag and drop
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    SVG, PNG, JPG or GIF (upto
                                                    10MB)
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-orange-400 text-white float-right mb-5">Submit</button>
                </form>
            </div>
        </div>
    </div>

        <div class="flex items-center justify-center w-full h-screen" v-if="userStatus == 'pending'">
            <div class="flex justify-center items-center space-x-1 text-sm text-gray-700">

                        <svg fill='none' class="w-6 h-6 animate-spin" viewBox="0 0 32 32" xmlns='http://www.w3.org/2000/svg'>
                            <path clip-rule='evenodd'
                                d='M15.165 8.53a.5.5 0 01-.404.58A7 7 0 1023 16a.5.5 0 011 0 8 8 0 11-9.416-7.874.5.5 0 01.58.404z'
                                fill='currentColor' fill-rule='evenodd' />
                        </svg>


                <div>Loading ...</div>
            </div>
        </div>

    <div class="max-w-[2520px] mt-20 xl:px-20 md:px-10 sm:px-2 px-4" v-if="userStatus == 'approved'">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">

            <div v-if="active == 9">
                <div>
                    <p class="text-2xl font-bold mt-1">
                        Step 10: Term & Condition
                    </p>

                    <p class="text-xs mt-1">Set up your terms and condition</p>
                    <hr class="my-5" />
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
                                    class="flex items-center justify-center w-full md:w-1/2"
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
            <div class="flex flex-col w-full" v-if="active == 1">
                <p class="text-2xl font-bold mt-1 mb-2">Step 1: Dorm Details</p>
                <hr class="mt-5" />
                <div class="py-6 border-b border-gray-100 dark:border-gray-800">
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
                                    <span class="text-xs text-red-500 ml-2">
                                        {{ errorMessages.property_name }}
                                    </span>
                                </div>

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
                                <span class="text-xs text-red-500 ml-2"
                                    >{{ errorMessages.description }}
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
                                    placeholder=""
                                />
                             <div>
                                <span class="text-xs text-red-500 ml-2"
                                    >{{ errorMessages.floors_total }}
                                </span>
                             </div>

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
                                    <span class="text-xs text-red-500 ml-2"
                                    >{{ errorMessages.rooms_total }}
                                    </span>
                                </div>

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
                                    <span class="text-xs text-red-500 ml-2"
                                    >{{ errorMessages.reservation }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-6 border-b border-gray-100 dark:border-gray-800">
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
                                    v-for="(commonArea, index) in commonAreas"
                                    :key="index"
                                >
                                    <div
                                        class="flex flex-row gap-1 mb-2 w-full"
                                    >
                                        <div class="w-full">
                                            <div
                                                class="flex flex-row justify-between items-center"
                                            >
                                                <p class="text-md font-bold">
                                                    Description:
                                                </p>
                                                <span
                                                    class="float-right cursor-pointer"
                                                    @click="
                                                        removeCommonAreas(index)
                                                    "
                                                >
                                                    <i
                                                        class="fa-solid fa-trash-can"
                                                    ></i>
                                                </span>
                                            </div>
                                            <select
                                                required
                                                v-model="commonArea.name"
                                                :class="{
                                                    'border-red-500':
                                                        !!errorMessages
                                                            .commonArea[index]
                                                            ?.name &&
                                                        !commonArea.name,
                                                }"
                                                class="w-full rounded-lg border-gray-300"
                                            >
                                                <option>Living Room</option>
                                                <option>Kitchen</option>
                                                <option>Bathroom</option>
                                            </select>
                                            <div
                                                v-if="
                                                    errorMessages.commonArea[
                                                        index
                                                    ]
                                                "
                                            >
                                                <p
                                                    class="text-xs text-red-500 ml-2"
                                                >
                                                    {{
                                                        errorMessages
                                                            .commonArea[index]
                                                            .name
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
                                            :id="'areas_image' + index"
                                            :ref="'areas_image_' + index"
                                            @change="
                                                roomAreasChange(
                                                    $event,
                                                    'areas_image' + index,
                                                    index
                                                )
                                            "
                                            accept="image/*"
                                            class="hidden"
                                        />
                                        <label
                                            :for="'areas_image' + index"
                                            :class="{
                                                'border-red-500':
                                                    !!errorMessages.commonArea[
                                                        index
                                                    ]?.src && !commonArea.src,
                                            }"
                                            class="flex flex-col items-center justify-center w-full h-64 bg-white border-2 border-gray-200 border-dashed rounded-lg cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500"
                                        >
                                            <img
                                                v-if="commonArea.src"
                                                :src="commonArea.src"
                                                alt="areas_image"
                                                class="h-64 w-full rounded-lg object-cover bg-no-repeat bg-center"
                                                @click="
                                                    roomAreasClick(
                                                        'areas_image' + index
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
                                                    SVG, PNG, JPG or GIF (upto
                                                    10MB)
                                                </p>
                                            </div>
                                            <div
                                                v-if="
                                                    errorMessages.commonArea[
                                                        index
                                                    ]
                                                "
                                            >
                                                <p
                                                    class="text-xs text-red-500 ml-2"
                                                >
                                                    {{
                                                        errorMessages
                                                            .commonArea[index]
                                                            .src
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
                <div class="w-full gap-2 grid grid-cols-1 md:grid-cols-3 px-3">
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
                                    for="furnished_desc"
                                    class="text-black"
                                    value="Furnished Description"
                                />
                                <TextInput
                                    id="furnished_desc"
                                    type="text"
                                    class="mt-1 block w-full text-black"
                                    placeholder="ex. With bed, refrigerator etc."
                                    v-model="room.furnished_desc"
                                    required
                                />
                                <div v-if="errorMessages.room[index]">
                                    <p class="text-xs text-red-500 ml-2">
                                    {{ errorMessages.room[index].furnished_desc }}
                                    </p>
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
                    <div class="w-full md:w-9/12">
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
                                        placeholder="e.g: 3months/1year"
                                    />
                                </div>
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
                    <div class="w-full md:w-9/12">
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

                <div class="w-full flex flex-col mt-5 px-1">
                    <div
                        class="w-full flex flex-row mt-2"
                        v-for="(rule, index) in rules"
                        :key="index"
                    >
                        <div>
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
            
            <div class="w-full" v-if="active == 8">
                <p class="text-2xl font-bold mt-1">
                    Step 9: Payment Method Setup
                </p>

                <p class="text-xs mt-1">
                    Set up your payment method to receive payments.
                </p>
                <hr class="mt-5" />
                <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-full md:w-9/12">
                        <div class="flex flex-wrap -m-3">
                            <div class="w-full p-3 md:w-1/3">
                                <div>
                                    <div class="flex gap-2 items-center">
                                        <label class="block font-bold mb-2"
                                            >Xendit</label
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
                                    <span class="text-xs text-gray-400">Required. Xendit</span>
                                </div>
                            </div>
                            <div class="w-full md:w-2/6 p-3">
                                <div class="flex flex-col">
                                    <p class="text-sm font-bold">Public Key:</p>
                                    <input
                                        id="pk"
                                        v-model="pk"
                                        :class="{
                                            'border-red-500':
                                                !!errorMessages.pk
                                        }"
                                        required=""
                                        placeholder="Xendit Public Key"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white"
                                    />
                                    <span class="text-xs text-red-500 ml-2"
                                        >{{ errorMessages.pk }}
                                    </span>
                                </div>
                                <div class="flex mt-4 flex-col">
                                    <p class="text-sm font-bold">Secret Key:</p>
                                    <input
                                        id="sk"
                                        v-model="sk"
                                        :class="{
                                            'border-red-500':
                                                !!errorMessages.sk
                                        }"
                                        required=""
                                        placeholder="Xendit Secret Key"
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white"
                                    />
                                    <span class="text-xs text-red-500 ml-2"
                                        >{{ errorMessages.sk }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="py-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-full md:w-9/12">
                        <div class="flex flex-wrap -m-3">
                            <div class="w-full p-3 md:w-1/3">
                                <div>
                                    <div class="flex gap-2 items-center">
                                        <label class="block font-bold mb-2"
                                            >Bank Details <span class="text-gray-400 text-sm">(Optional)</span></label
                                        >
                                    </div>
                                    <span class="text-xs text-gray-400">Optional. Bank Details</span>
                                </div>
                            </div>
                            <div class="w-full md:w-2/6 p-3">
                                <div class="flex flex-col">
                                    <p class="text-sm font-bold">Bank Name:</p>
                                    <input
                                        id="bank_name"
                                        v-model="bank_name"
                                        required
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white"
                                    />
                                </div>
                                <div class="flex mt-4 flex-col">
                                    <p class="text-sm font-bold">Account Name:</p>
                                    <input
                                        id="account_name"
                                        v-model="account_name"
                                        placeholder=""
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white"
                                    />
                                </div>
                                <div class="flex mt-4 flex-col">
                                    <p class="text-sm font-bold">Account Number:</p>
                                    <input
                                        id="account_number"
                                        v-model="account_number"
                                        required
                                        placeholder=""
                                        type="text"
                                        class="px-3 py-2 max-w-full focus:ring focus:outline-none border-gray-700 rounded w-full 12 border bg-white"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

            </div>
            <div class="w-full" v-if="active == 10">
                <div clas="w-full grid grid-cols-3 gap-5">
                    <div className=" max-w-screen-lg mx-auto ">
                        <p class="text-4xl font-black text-orange-400">You're almost there! Complete your dormitory listing</p>

                        <div>
                            <p class="text-3xl font-extrabold mt-5 text-black">1. Select Subscription</p>
                            <div class="w-full grid grid-cols-1 md:grid-cols-5 gap-10 py-5">
                                <ul class="col-span-3 md:col-span-2 ">
                                    <li v-for="subscription in subscriptions" :key="subscription.value" class="mt-4">
                                        <input type="radio" :id="subscription.value" name="subscription" :value="subscription" class="hidden peer" v-model="selectedSubscription" required>
                                        <label :for="subscription.value" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-orange-600 peer-checked:text-orange-400 hover:text-gray-600 hover:bg-gray-100 ">
                                        <div class="block">
                                            <div class="w-full text-lg font-semibold">{{ subscription.label }}</div>
                                            <div class="w-full">{{ subscription.description }}</div>
                                            <div class="w-full">{{ moneyFormat(subscription.price) }} /month</div>
                                        </div>
                                        <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                        </svg>
                                        </label>
                                    </li>
                                </ul>
                                <div class="col-span-3 mt-4">
                                    <div>
                                        <div class=" bg-white border rounded-lg shadow px-4 pt-8 lg:mt-0">
                                            <p class="text-xl font-medium">Payment Details</p>
                                            <p class="text-gray-400">Complete by providing your payment details.</p>
                                            <!--Payment Method-->
                                                <div class="w-full">
                                                    <label class="text-sm font-semibold">Name:</label>
                                                    <TextInput
                                                        type="text"
                                                        class="placeholder:text-gray-400 w-full"
                                                    />
                                                </div>
                                            <!--Total-->
                                            <div class="mt-6 border-t border-b py-2">
                                                <p class="text-md font-bold text-gray-900">Subscription</p>
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">{{ selectedSubscription ? selectedSubscription.label : 'Select a subscription' }}</p>
                                                    <p class="font-semibold text-gray-900">{{ moneyFormat(selectedSubscription.value ? selectedSubscription.price : 0) }} /month</p>
                                                </div>
                                                </div>
                                                <div class="mt-6 flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">Total</p>
                                                <p class="text-2xl font-semibold text-gray-900">{{ moneyFormat(selectedSubscription? selectedSubscription.price : 0) }}</p>
                                            </div>
                                            <hr class="my-4"/>
                                            <div class="w-full mt-2">
                                                <input
                                                    type="checkbox"
                                                    value="I guarantee that all details provided are accurate and true."
                                                    v-model="termsAndCondition"
                                                />
                                                <label class="ml-2 text-sm"
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
                                            <Toast />
                                            <ConfirmDialog />
                                            <button
                                                @click="saveDorm()"
                                                :class="{
                                                    'cursor-not-allowed bg-orange-200':
                                                        loading || termsAndCondition.length < 2,
                                                    'bg-orange-500 text-white':
                                                        !loading && termsAndCondition.length === 2,
                                                }"
                                                class="mt-4 mb-8 w-full rounded-md bg-orange-400 px-6 py-3 font-medium text-white">
                                                Proceed to Payment
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center mt-5 justify-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <p class="text-gray-800 font-bold">Payment processed by:</p>
                                            <Image src="/images/xenditlogo.png" alt="xendit logo" width="220" />
                                        </div>
                                        <div>
                                            <Image src="/images/credicardlogo.png" alt="Credit card logo" width="200" />
                                        </div>
                                        <div class="flex flex-row mt-3 gap-3">
                                            <Image src="/images/paymayalogo.png" alt="Maya logo" width="100" />
                                            <Image src="/images/grablogo.png" alt="Grab pay logo" width="50" />
                                            <Image src="/images/gcashlogo.png" alt="Gcash logo" width="100" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <!--Button-->
            <div
                class="items-center mt-5 space-x-2 border-gray-200 rounded-b dark:border-gray-600"
            >
                <div class="w-full">
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
                        v-if="active < 10"
                        @click="handleNext()"
                        type="button"
                        class="text-gray-500 float-right bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        Next
                    </button>
                    <button
                        data-modal-hide="defaultModal"
                        v-if="active == 10"
                        @click="saveDorm()"
                        type="button"

                        class="text-gray-500 float-right bg-white focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                    >
                        {{ !!loading ? "Saving..." : "Submit" }}
                    </button>
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
                                    Setup Xendit Account
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
                                        >1. Sign up for a Xendit
                                        Account:</span
                                    >
                                    <p class="font-light">
                                        If you haven't already, go to the
                                        <a
                                            href="https://shorturl.at/klpEO"
                                            class="text-orange-400 underline cursor-pointer hover:text-opacity-25"
                                            >Xendit website</a
                                        >
                                        and sign up for an account. You'll need
                                        a Xendt account to access your API
                                        keys.
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <span class="font-bold"
                                        >2. Log in to Your Xendit
                                        Dashboard:</span
                                    >
                                    <p class="font-light">
                                        After creating an account, log in to
                                        your Xendit dashboard.
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <span class="font-bold"
                                        >3. Navigate to API Keys:</span
                                    >
                                    <p class="font-light">
                                        Once you're logged in, you can find your
                                        API keys in the dashboard. Scroll down and you will see
                                        manage api keys."
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <span class="font-bold"
                                        >4. Generate API Keys:</span
                                    >
                                    <p class="font-light">
                                        In the API Keys section, you'll see an
                                        option to generate your keys. Click on
                                        "Create a key." You'll typically need
                                        two types of keys: a Secret Key (SK) for
                                        server-side operations and a Publishable
                                        Key (PK) for client-side operations.
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <span class="font-bold"
                                        >5. Copy and Save Your Keys:</span
                                    >
                                    <p class="font-light">
                                        After generating your keys, Xendit
                                        will display them on the dashboard. Make
                                        sure to copy them and store them
                                        securely.
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <span class="font-bold"
                                        >6. Paste Your Keys:</span
                                    >
                                    <p class="font-light">
                                        After copying your keys, input them on
                                        the input field, PK for publik keys, SK
                                        for secret key
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
    </div>
</template>
