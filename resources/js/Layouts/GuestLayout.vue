<!-- <script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);

const isMobileView = ref(false)

isMobileView.value = screen.width < 600;

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    dorms: Array
});

</script> -->

<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Terms from "@/Components/Terms.vue"; //dinagdag ko pati nasa component
import VsToast from "@vuesimple/vs-toast";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import InputMask from 'primevue/inputmask';
export default {
    components: {
        ApplicationLogo,
        Link,
        InputLabel,
        TextInput,
        PrimaryButton,
        InputError,
        Terms,
        Checkbox,
        VsToast,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        InputMask,  
    },
    methods: {
        togglePasswordVisibility(field) {
            const passwordField = document.getElementById(field);
            if (passwordField) {
                passwordField.type =
                    passwordField.type === "password" ? "text" : "password";
            }
        },
    },
    setup() {
        const openRegisterModal = () => {
            var modal = document.getElementById("registerModal");

            modal.style.display = "block";

            form.reset();
        };

        const closeRegisterModal = () => {
            var modal = document.getElementById("registerModal");

            modal.style.display = "none";
        };

        //Dinagdag ko
        const openTermsModal = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "block";
        };

        const closeTermsModal = () => {
            var modal = document.getElementById("defaultModal");
            modal.style.display = "none";
        };

        const openTutModal = () => {
            var modal = document.getElementById("tutModal");

            modal.style.display = "block";
        };

        const closeTutModal = () => {
            var modal = document.getElementById("tutModal");

            modal.style.display = "none";
        };

        const selectUserType = (userType) => {
            form.user_type = userType;
        };
        //Hanggang dito

        const openLoginModal = () => {
            var modal = document.getElementById("loginModal");

            modal.style.display = "block";

            loginForm.reset();
        };

        const closeLoginModal = () => {
            var modal = document.getElementById("loginModal");

            modal.style.display = "none";
        };

        const isDropdownOpen = ref(false);

    // Function to toggle dropdown visibility
    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value;
    };

        const showingNavigationDropdown = ref(false);

        const isMobileView = ref(false);

        isMobileView.value = screen.width < 600;

        const page = usePage();

        const form = useForm({
            first_name: "",
            middle_name: "",
            last_name: "",
            phone_number: "",
            username: "",
            user_type: "",
            password: "",
            password_confirmation: "",
            terms: false,
            code: "",
        });

        const acceptClose = () => {
            var modal = document.getElementById("defaultModal");
            form.terms = true;
            modal.style.display = "none";
        };

        const idPictureClick = () => {
            document.getElementById("id_picture").click();
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
        const SelfieidPictureClick = () => {
            document.getElementById("selfie_id_picture").click();
        };

        const SelfieidPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                console.log(e);
                form.selfie_id_picture = e.target.result;
            };
        };
        //hanggagn dito

        const openModal = () => {
            var modal = document.getElementById("otpModal");

            modal.style.display = "block";
        };

        const closeModal = () => {
            var modal = document.getElementById("otpModal");

            modal.style.display = "none";
        };

        const isLoadingRegister = ref(false);

        const submit = () => {
            form.phone_number = form.phone_number.replace(/\s/g, '');
            
            if (validateForm()) {
                axios
                    .post(route("send.otp"), form)
                    .then((response) => {
                        openModal();
                        console.log(form.code);
                    })
                    .catch((error) => {});
            } else {
                VsToast.show({
                    title: "Error",
                    message: "Please input all fields",
                    variant: "error",
                });
                console.log(form);
            }
        };

        const removeSpaces = () => {
            stringWithoutSpaces.value = inputString.value.replace(/\s/g, '');
        };


        const confirmSubmit = () => {
            isLoadingRegister.value = true;

            form.post(route("user.register"), {
                onSuccess: () => {
                    location.reload();
                    VsToast.show({
                        title: "Registered",
                        message: "You've registered successfuly",
                        variant: "success",
                    });

                    isLoadingRegister.value = false;
                },
                onError: (error) => {
                    VsToast.show({
                        title: "Error",
                        message: error,
                        variant: "error",
                    });

                    isLoadingRegister.value = false;
                },
            });
        };

        const loginForm = useForm({
            username: "",
            password: "",
            remember: false,
        });

        const not_approved = ref(null);

        const login = () => {
            not_approved.value = null;

            loginForm.post(route("login"), {
                onSuccess: (res) => {
                    // console.log(res)
                    VsToast.show({
                        title: "Logged In",
                        message: "You've login successfuly",
                        variant: "success",
                    });
                },
                onError: (error) => {
                    VsToast.show({
                        title: "Error",
                        message: error.not_approved,
                        variant: "error",
                    });
                },
            });
        };
        const terms = page.props.terms;
        const errorMessages = ref({
            first_name: "", // Add corresponding error messages for your textbox fields
            last_name: "",
            phone_number: "",
            username: "",
            user_type: "",
            terms: "",
        });

        const validateForm = () => {
            let isValid = true;
            errorMessages.value.first_name = "";
            errorMessages.value.last_name = "";
            errorMessages.value.phone_number = "";
            errorMessages.value.username = "";
            errorMessages.value.user_type = "";
            errorMessages.value.id_picture = "";
            errorMessages.value.selfie_id_picture = "";
            errorMessages.value.terms = "";

            if (form.first_name.trim() === "") {
                isValid = false;
                errorMessages.value.first_name = "First name is required";
            }
            if (form.last_name.trim() === "") {
                isValid = false;
                errorMessages.value.last_name = "Last name is required";
            }
            if (form.phone_number.trim() === "") {
                isValid = false;
                errorMessages.value.phone_number = "Phone number is required";
            }
            if (form.username.trim() === "") {
                isValid = false;
                errorMessages.value.username = "Usernumber is required";
            }
            if (form.user_type.trim() === "") {
                isValid = false;
                errorMessages.value.user_type = "User Type is required";
            }
            if (!validatePassword()) {
                isValid = false;
            }
            if (!form.terms) {
                isValid = false;
                errorMessages.value.terms =
                    "You must accept the terms and conditions";
            }

            return isValid;
        };
        const errorMessagespassword = ref({
            password: "",
            password_length: "",
            password_uppercase: "",
            password_lowercase: "",
            password_number: "",
            password_special: "",
            password_confirmation: "",
        });

        const passwordTouched = ref(false);
        const confirmPasswordTouched = ref(false);

        const validatePassword = () => {
            let isValid = true;
            errorMessagespassword.value.password_length = "";
            errorMessagespassword.value.password_uppercase = "";
            errorMessagespassword.value.password_lowercase = "";
            errorMessagespassword.value.password_number = "";
            errorMessagespassword.value.password_special = "";
            errorMessagespassword.value.confirm_password = "";

            if (!passwordTouched.value && !confirmPasswordTouched.value) {
                return true;
            }

            const passwordRegex =
                /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordRegex.test(form.password)) {
                isValid = false;
                errorMessagespassword.value.password_length =
                    "Password must be at least 8 characters.";
            }

            if (!/[A-Z]/.test(form.password)) {
                isValid = false;
                errorMessagespassword.value.password_uppercase =
                    "Password must contain at least one uppercase letter.";
            }

            if (!/[a-z]/.test(form.password)) {
                isValid = false;
                errorMessagespassword.value.password_lowercase =
                    "Password must contain at least one lowercase letter.";
            }

            if (!/\d/.test(form.password)) {
                isValid = false;
                errorMessagespassword.value.password_number =
                    "Password must contain at least one number.";
            }

            if (!/[@$!%*?&]/.test(form.password)) {
                isValid = false;
                errorMessagespassword.value.password_special =
                    "Password must contain at least one special character.";
            }

            if (form.password_confirmation != form.password) {
                isValid = false;
                errorMessagespassword.value.confirm_password =
                    "Passwords do not match.";
            }

            return isValid;
        };

        const handlePasswordChange = () => {
            validatePassword();
        };
        const handleConfirmPasswordChange = () => {
            validatePassword();
        };

        console.log(page.props);
        return {
            terms,
            handlePasswordChange,
            selectUserType,
            errorMessages,
            errorMessagespassword,
            showingNavigationDropdown,
            isMobileView,
            handleConfirmPasswordChange,
            confirmPasswordTouched,
            passwordTouched,
            form,
            loginForm,
            openRegisterModal,
            closeRegisterModal,
            openLoginModal,
            closeLoginModal,
            openTermsModal,
            closeTermsModal,
            openTutModal,
            closeTutModal,
            idPictureClick,
            idPictureChange,
            SelfieidPictureClick,
            SelfieidPictureChange,
            submit,
            login,
            openModal,
            closeModal,
            confirmSubmit,
            acceptClose,
            not_approved,
            isLoadingRegister,

            isDropdownOpen,

            toggleDropdown,
        };
    },
};
</script>

<template>
    <div>
        <div class="h-full">
            <nav class="fixed top-0 z-50 w-full bg-white">
                <div class="py-4">
                    <div
                        class="max-w-[2520px] mx-auto xl:px-20 md:px-10 sm:px-2 px-4"
                    >
                        <div
                            class="flex flex-row items-center justify-between gap-3 md:gap-0"
                        >
                            <div class="flex items-center justify-start">
                                <div class="flex items-center">
                            <AppDropdown>
                                <button
                                    class="inline-flex items-center p-2 text-sm rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-100 dark:focus:ring-gray-600"
                                >
                                    <span class="sr-only">Open sidebar</span>
                                    <svg
                                        class="w-6 h-6"
                                        aria-hidden="true"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            clip-rule="evenodd"
                                            fill-rule="evenodd"
                                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
                                        ></path>
                                    </svg>
                                </button>
                                <AppDropdownContent class="left-0 origin-top-left lg:hidden block">
                                    <AppDropdownItem>
                                        <Link href="/" >Find a home</Link>
                                    </AppDropdownItem>
                                    <AppDropdownItem>
                                        <Link :href="route('about.us')" >Pricing</Link>
                                    </AppDropdownItem>
                                    <AppDropdownItem>
                                        <Link :href="route('about.us')" class="hover:text-orange-400">About us</Link>
                                    </AppDropdownItem>
                                    <AppDropdownItem>
                                        <Link :href="route('user.help')" class="hover:text-orange-400">FAQ</Link>
                                    </AppDropdownItem>
                                </AppDropdownContent>
                            </AppDropdown>
                                </div>
                            
                                <a href="/" class="ml-2 md:mr-24">
                                    <ApplicationLogo />
                                </a>
                            </div>
                            <div class="flex flex-row gap-5 items-center">
                                <div
                                    class="lg:flex-row flex-col z-50 bg-white gap-5 items-center hidden sm:hidden md:hidden lg:flex"
                                >
                                    <!-- Button for large screens -->
                                    <Link href="/" class="hover:text-orange-400">Find a dorm</Link>
                                    <Link :href="route('about.us')" class="hover:text-orange-400">Pricing</Link>
                                    <Link :href="route('about.us')" class="hover:text-orange-400">About us</Link>
                                    <Link :href="route('user.help')" class="hover:text-orange-400">FAQ</Link>
                                </div>

                                

                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click.prevent="openLoginModal()"
                                        class="bg-transparent hover:bg-orange-400 text-orange-400 font-semibold hover:text-white py-2 px-4 border border-orange-400 hover:border-transparent rounded"
                                    >
                                        Signin
                                    </button>

                                    <button
                                        @click.prevent="openRegisterModal()"
                                        class="bg-orange-400 hover:bg-transparent text-white font-semibold hover:text-orange-400 py-2 px-4 border border-transparent hover:border-orange-400 rounded"
                                    >
                                        Signup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>


            <!--New UI register modal-->
            <div
                id="registerModal"
                aria-hidden="true"
                style="background-color: rgba(0, 0, 0, 0.7)"
                class="registerModal fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            >
                <div class="h-screen flex justify-center items-center">
                    <div class="relative w-full max-w-2xl max-h-full">
                        <div
                            class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                        >
                            <button
                                type="button"
                                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="authentication-modal"
                                @click="closeRegisterModal()"
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

                            <div id="otpModal" class="otpModal mt-10 md:mt-0">
                                <div
                                    class="otp-modal-content flex flex-col"
                                    :style="{
                                        width: isMobileView ? '97%' : '30%',
                                    }"
                                >
                                    <div class="w-full">
                                        <span> OTP </span>

                                        <span
                                            class="float-right cursor-pointer"
                                            @click="closeModal()"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                        </span>
                                    </div>

                                    <div class="w-full mt-5">
                                        <input
                                            type="text"
                                            class="w-full rounded-md h-[40px]"
                                            placeholder="Code"
                                            v-model="form.code"
                                        />
                                    </div>

                                    <div class="w-full mt-5">
                                        <button
                                            class="float-right rounded-md px-4 py-2 bg-cyan-300"
                                            :disabled="isLoadingRegister"
                                            :class="{
                                                'cursor-not-allowed':
                                                    isLoadingRegister,
                                            }"
                                            @click="confirmSubmit()"
                                        >
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-6 lg:px-8">
                                <div class="mb-4">
                                    <h3
                                        class="text-xl font-medium text-gray-900 dark:text-white"
                                    >
                                        Welcome to Dormhub!
                                    </h3>
                                    <span
                                        class="mb-4 text-sm font-medium text-gray-900 dark:text-white"
                                        >Create an account</span
                                    >
                                </div>

                                <hr class="mb-4" />
                                <!-- Sign Up -->
                                <div class="mb-4">
                                    <InputLabel
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        value="Register as ?"
                                    />
                                    <div class="grid grid-cols-2 gap-2">
                                        <button
                                            @click="selectUserType('tenant')"
                                            :class="{
                                                'bg-orange-500':
                                                    form.user_type === 'tenant',
                                                'bg-transparent':
                                                    form.user_type !== 'tenant',
                                            }"
                                            class="px-4 py-2 rounded-lg border hover:bg-orange-400 text-black dark:text-white"
                                        >
                                            Tenant
                                        </button>
                                        <button
                                            @click="selectUserType('owner')"
                                            :class="{
                                                'bg-orange-500':
                                                    form.user_type === 'owner',
                                                'bg-transparent':
                                                    form.user_type !== 'owner',
                                            }"
                                            class="px-4 py-2 rounded-lg border hover:bg-orange-400 text-black dark:text-white"
                                        >
                                            Dorm Owner
                                        </button>
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="errorMessages.user_type"
                                    />
                                </div>
                                <hr class="mb-4" />
                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <div>
                                        <InputLabel
                                            for="first_name"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            value="First Name"
                                        />

                                        <TextInput
                                            id="first_name"
                                            type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                            placeholder="ex: Juan"
                                            v-model="form.first_name"
                                            required
                                            autofocus
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="errorMessages.first_name"
                                        />
                                    </div>

                                    <div>
                                        
                                    <div>
                                        <InputLabel
                                            for="last_name"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            value="Last Name"
                                        />

                                        <TextInput
                                            id="last_name"
                                            type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                            placeholder="ex: De La Cruz"
                                            v-model="form.last_name"
                                            required
                                            autofocus
                                            autocomplete="last_name"
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="errorMessages.last_name"
                                        />
                                    </div>
                                    </div>
                                </div>

                                    <div>
                                        <InputLabel
                                            for="phone_number"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            value="Phone number"
                                        />

                                        <vue-tel-input 
                                            v-model="form.phone_number"
                                            autoFormat
                                            validCharactersOnly
                                            :maxlength = '16'
                                        ></vue-tel-input>
                                                                            
                                        <InputError
                                            class="mt-2"
                                            :message="
                                                errorMessages.phone_number
                                            "
                                        />
                                    </div>

                                <div class="my-2">
                                    <InputLabel
                                        for="email-login"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        value="Your Username"
                                    />

                                    <TextInput
                                        id="email-login"
                                        type="text"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        v-model="form.username"
                                        required
                                        autofocus
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="errorMessages.username"
                                    />
                                </div>

                                <div class="mb-2">
                                    <InputLabel
                                        for="password-login"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        value="Your Password"
                                    />

                                    <div class="relative">
                                        <TextInput
                                            id="password-login"
                                            type="password"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                            
                                            v-model="form.password"
                                            required
                                            @input="handlePasswordChange()"
                                            @focus="passwordTouched = true"
                                        />
                                        <button
                                            class="absolute top-1/2 right-3 transform -translate-y-1/2 cursor-pointer"
                                            @click="
                                                togglePasswordVisibility(
                                                    'password-login'
                                                )
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                class="h-5 w-5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errorMessagespassword.password_length
                                        "
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errorMessagespassword.password_uppercase
                                        "
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errorMessagespassword.password_lowercase
                                        "
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errorMessagespassword.password_number
                                        "
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errorMessagespassword.password_special
                                        "
                                    />
                                </div>

                                <div class="mb-2">
                                    <InputLabel
                                        for="password_confirmation"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        value="Confirm Password"
                                    />

                                    <div class="relative">
                                        <TextInput
                                            id="password_confirmation"
                                            type="password"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                         
                                            v-model="form.password_confirmation"
                                            required
                                            @input="handleConfirmPasswordChange"
                                            @focus="
                                                confirmPasswordTouched = true
                                            "
                                        />
                                        <button
                                            class="absolute top-1/2 right-3 transform -translate-y-1/2 cursor-pointer"
                                            @click="
                                                togglePasswordVisibility(
                                                    'password_confirmation'
                                                )
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                class="h-5 w-5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="
                                            errorMessagespassword.confirm_password
                                        "
                                    />
                                </div>

                                <hr class="mb-4 bg-gray-400" />
                                <div class="flex justify-between mb-2">
                                    <div class="flex items-start gap-2">
                                        <Checkbox
                                            name="terms"
                                            v-model:checked="form.terms"
                                        />
                                        <div
                                            class="text-sm font-medium text-gray-500 dark:text-gray-300"
                                        >
                                            I agreed to the
                                            <a
                                                @click="openTermsModal()"
                                                class="hover:underline text-orange-500"
                                                >Terms and Condition</a
                                            >
                                        </div>
                                    </div>
                                </div>
                                <InputError
                                    class="mt-2"
                                    :message="errorMessages.terms"
                                />
                                <button
                                    :class="{
                                        'opacity-25': form.processing,
                                    }"
                                    :disabled="form.processing"
                                    class="w-full text-white my-3 bg-orange-500 hover:bg-orange-400 focus:ring-4 focus:outline-none focus:ring-orange-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center0"
                                    @click="submit()"
                                >
                                    Sign up
                                </button>
                                <div
                                    class="text-sm font-medium text-gray-500 mt-2 dark:text-gray-300"
                                >
                                    Already registered?
                                    <a
                                        @click="
                                            openLoginModal(),
                                                closeRegisterModal()
                                        "
                                        class="hover:underline text-orange-500"
                                        >Signin Now</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms modal -->
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
                                    {{ terms ? terms.title : "" }}
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
                                <Terms :content="terms ? terms.content : ''" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                            >
                                <button
                                    @click="acceptClose()"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                >
                                    I accept
                                </button>
                                <button
                                    @click="closeTermsModal()"
                                    type="button"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                                >
                                    Decline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Tutorial modal-->
            <div
                id="tutModal"
                tabindex="-1"
                aria-hidden="true"
                style="background-color: rgba(0, 0, 0, 0.7)"
                class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            >
                <div class="h-screen flex justify-center items-center">
                    <div class="relative w-full max-w-2xl max-h-full">
                        <!-- Modal content -->
                        <div
                            class="relative bg-gray-700 rounded-lg shadow dark:bg-white"
                        >
                            <!-- Modal header -->
                            <div
                                class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                            >
                                <h3
                                    class="text-xl font-semibold text-white dark:text-black"
                                >
                                    Secure User Verification: A Step-by-Step
                                    Guide to Uploading Valid ID and Selfie with
                                    Valid ID
                                </h3>
                                <button
                                    type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    @click="closeTutModal()"
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
                            <div class="p-6 space-y-6"></div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                            >
                                <button
                                    @click="closeTutModal()"
                                    type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                >
                                    I understood
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--New UI login-->
            <div
                id="loginModal"
                tabindex="-1"
                aria-hidden="true"
                style="background-color: rgba(0, 0, 0, 0.7)"
                class="loginmodal fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
            >
                <div class="h-screen flex justify-center items-center">
                    <div class="relative w-full max-w-md max-h-full">
                        <div
                            class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                        >
                            <button
                                type="button"
                                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="authentication-modal"
                                @click="closeLoginModal()"
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
                            <div class="px-6 py-6 lg:px-8">
                                <div class="mb-4">
                                    <h3
                                        class="text-xl font-medium text-gray-900 dark:text-white"
                                    >
                                        Welcome to Dormhub!
                                    </h3>
                                    <span
                                        class="mb-4 text-sm font-medium text-gray-900 dark:text-white"
                                        >Signin to your account</span
                                    >
                                </div>
                                <form class="space-y-6" @submit.prevent="login">
                                    <div>
                                        <InputLabel
                                            for="email-login"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            value="Your Username"
                                        />

                                        <TextInput
                                            id="email-login"
                                            type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                            v-model="loginForm.username"
                                            required
                                            autofocus
                                            autocomplete="email"
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.username"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel
                                            for="password-login"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            value="Your Password"
                                        />

                                        <TextInput
                                            id="password-login"
                                            type="password"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                     
                                            v-model="loginForm.password"
                                            required
                                            autofocus
                                            autocomplete="current-password"
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.password"
                                        />
                                    </div>
                                    <div class="flex justify-between">
                                        <div class="flex items-start">
                                            <Checkbox
                                                name="remember"
                                                v-model:checked="
                                                    loginForm.remember
                                                "
                                            />
                                            <span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-300"
                                                >Remember me</span
                                            >
                                        </div>
                                        <a
                                            href="#"
                                            class="text-xs hover:underline text-red-500"
                                            >Lost Password?</a
                                        >
                                    </div>
                                    <button
                                        :class="{
                                            'opacity-25': loginForm.processing,
                                        }"
                                        :disabled="loginForm.processing"
                                        class="w-full text-white bg-orange-500 hover:bg-orange-400 focus:ring-4 focus:outline-none focus:ring-orange-600 font-medium rounded-lg text-sm px-5 py-2.5 text-center0"
                                    >
                                        Signin
                                    </button>

                                    <div class="w-full" v-if="not_approved">
                                        <span class="text-xs text-rose-500">
                                            {{ not_approved }}
                                        </span>
                                    </div>
                                    <div
                                        class="text-sm font-medium text-gray-500 dark:text-gray-300"
                                    >
                                        Not registered?
                                        <a
                                            @click="
                                                openRegisterModal(),
                                                    closeLoginModal()
                                            "
                                            class="hover:underline text-orange-500"
                                            >Create an account</a
                                        >
                                        <div class="text-center"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <main>
            <slot />
        </main>
        <footer
            class="bottom-0 fixed w-full z-20 bg-white rounded-lg border dark:bg-gray-800"
        >
            <div class="mx-auto p-4 md:flex md:items-center md:justify-between">
                <span
                    class="text-sm text-gray-500 sm:text-center dark:text-gray-400"
                    >© 2023
                    <a href="https://flowbite.com/" class="hover:underline"
                        >Dormhub™</a
                    >. All Rights Reserved.</span
                >
                <ul
                    class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0"
                >
                    <li>
                        <a
                            :href="route('about.us')"
                            class="mr-4 hover:underline md:mr-6"
                            >About us</a
                        >
                    </li>
                    <li>
                        <a
                            :href="route('privacy.policy')"
                            class="mr-4 hover:underline md:mr-6"
                            >Privacy Policy</a
                        >
                    </li>
                    <li>
                        <a :href="route('contact.us')" class="hover:underline"
                            >Contact us</a
                        >
                    </li>
                    <li>
                        <a
                            :href="route('user.help')"
                            class="mr-4 hover:underline md:mr-6"
                            >Help</a
                        >
                    </li>
                </ul>
            </div>
        </footer>
    </div>
</template>

<style>
.main {
    height: 100%;
    min-height: 92vh;
    background-color: #ffffff;
}

.bg-landing-page {
    background-color: #eb984e;
}

.register-btn {
    background-color: #5499c7;
    width: 150px;
}

.center {
    display: flex;
    justify-content: center;
    align-items: center;
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

.otpModal {
    display: none;
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 150px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0, 0, 0); /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
}

/* Modal Content */
.otp-modal-content {
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
