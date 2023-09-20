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
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import DormList from '@/Components/DormList.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
    import { ref, onMounted } from 'vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import Checkbox from '@/Components/Checkbox.vue';

    export default {
        components: {
            ApplicationLogo,
            Link,
            DormList,
            InputLabel,
            TextInput,
            PrimaryButton,
            InputError,
            Checkbox
        },
        setup(){
            const openRegisterModal = () => {
                var modal = document.getElementById("registerModal");

                modal.style.display = "block";

                form.reset()
            }

            const closeRegisterModal = () => {
                var modal = document.getElementById("registerModal");

                modal.style.display = "none";
            }

            const openLoginModal = () => {
                var modal = document.getElementById("loginModal");

                modal.style.display = "block";

                loginForm.reset()
            }

            const closeLoginModal = () => {
                var modal = document.getElementById("loginModal");

                modal.style.display = "none";
            }

            const showingNavigationDropdown = ref(false);

            const isMobileView = ref(false)

            isMobileView.value = screen.width < 600;

            const page = usePage()

            const dorms = ref([])

            onMounted(() => {
                dorms.value = page.props.dorms
            });

            const form = useForm({
                first_name: '',
                middle_name: '',
                last_name: '',
                phone_number: '',
                email: '',
                user_type: '',
                password: '',
                password_confirmation: '',
                id_picture: '',
                terms: false,
            });

            const idPictureClick = () => {
                document.getElementById('id_picture').click()
            }

            const idPictureChange = (e) => {
                const image = e.target.files[0];

                const reader = new FileReader();

                reader.readAsDataURL(image);

                reader.onload = e =>{
                    console.log(e)
                    form.id_picture = e.target.result
                }
            }

            const submit = () => {
                form.post(route('user.register'), {
                    // onFinish: () => form.reset('password', 'password_confirmation'),
                });
            };

            const loginForm = useForm({
                email: '',
                password: '',
                remember: false,
            });

            const login = () => {
                loginForm.post(route('login'), {});
            }

            return {
                dorms,
                showingNavigationDropdown,
                isMobileView,
                form,
                loginForm,
                openRegisterModal,
                closeRegisterModal,
                openLoginModal,
                closeLoginModal,
                idPictureClick,
                idPictureChange,
                submit,
                login
            }
        }
    }
</script>

<template>
    <div>
        <div class="h-full">
            <nav class="bg-white border-b border-gray-300 py-4">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between nav-bar-h relative">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex absolute right-0">
                                <span
                                    @click.prevent="openLoginModal()"
                                    class="ml-4 font-semibold text-black mt-2 cursor-pointer"
                                    >LOGIN
                                </span>

                                <span
                                    @click.prevent="openRegisterModal()"
                                    class="ml-4 font-semibold text-black mt-2 cursor-pointer"
                                    >SIGN UP
                                </span>
                            </div>
                        </div>



                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <div class="flex flex-col">
                            <div class="w-full mt-2">
                                <span
                                    @click.prevent="openLoginModal()"
                                    class="ml-4 font-semibold text-black mt-2 cursor-pointer"
                                    >LOGIN
                                </span>
                            </div>

                            <div class="w-full mt-2">
                                <span
                                    @click="openRegisterModal()"
                                    class="ml-4 font-semibold text-black mt-3"
                                    >SIGN UP</span
                                >
                            </div>
                        </div>
                    </div>

                </div>

            </nav>

            <div class="w-full main flex flex-col">

                <div class="w-full">
                    <DormList :dorms.sync="dorms" :user.sync="null"/>
                </div>


                <div class="w-full">
                    <div id="registerModal" class="registerModal md:mt-0">
                        <div class="register-modal-content flex flex-col" :style="{width: isMobileView ? '90%' : '40%'}">
                            <div class="w-full">
                                <span class="text-2xl font-bold ">
                                    SIGN UP
                                </span>
                                <span class="float-right cursor-pointer"
                                    @click="closeRegisterModal()"
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </span>
                            </div>

                            <div class="w-full mt-10">
                                <form @submit.prevent="submit">

                                    <div class="flex flex-row w-full">
                                        <div class="w-full mr-1">
                                            <InputLabel for="first_name" value="First Name" />

                                            <TextInput
                                                id="first_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.first_name"
                                                required
                                                autofocus
                                                autocomplete="first_name"
                                            />

                                            <InputError class="mt-2" :message="form.errors.first_name" />
                                        </div>

                                        <div class="w-full mr-1">
                                            <InputLabel for="middle_name" value="Middle Name" />

                                            <TextInput
                                                id="middle_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.middle_name"
                                                autofocus
                                                autocomplete="middle_name"
                                            />

                                            <InputError class="mt-2" :message="form.errors.middle_name" />
                                        </div>

                                        <div class="w-full">
                                            <InputLabel for="last_name" value="Last Name" />

                                            <TextInput
                                                id="last_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.last_name"
                                                required
                                                autofocus
                                                autocomplete="last_name"
                                            />

                                            <InputError class="mt-2" :message="form.errors.last_name" />
                                        </div>
                                    </div>


                                    <div class="flex flex-row w-full mt-4">
                                        <div class="w-full mr-1">
                                            <InputLabel for="user_type" value="Register as ?" />

                                            <select class="w-full mt-1 rounded-md" v-model="form.user_type">
                                                <option value="tenant">Tenant</option>
                                                <option value="owner">Dorm Owner</option>
                                            </select>

                                            <InputError class="mt-2" :message="form.errors.user_type" />
                                        </div>

                                        <div class="w-full mr-1">
                                            <InputLabel for="phone_number" value="Contact" />

                                            <TextInput
                                                id="phone_number"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.phone_number"
                                                required
                                                autocomplete="phone_number"
                                            />

                                            <InputError class="mt-2" :message="form.errors.phone_number" />
                                        </div>

                                        <div class="w-full">
                                            <InputLabel for="email" value="Email" />

                                            <TextInput
                                                id="email"
                                                type="email"
                                                class="mt-1 block w-full"
                                                v-model="form.email"
                                                required
                                                autocomplete="email"
                                            />

                                            <InputError class="mt-2" :message="form.errors.email" />
                                        </div>
                                    </div>

                                    <div class="flex flex-row w-full mt-4">
                                        <div class="w-full mr-1">
                                            <InputLabel for="password" value="Password" />

                                            <TextInput
                                                id="password"
                                                type="password"
                                                class="mt-1 block w-full"
                                                v-model="form.password"
                                                required
                                                autocomplete="new-password"
                                            />

                                            <InputError class="mt-2" :message="form.errors.password" />
                                        </div>

                                        <div class="w-full">
                                            <InputLabel for="password_confirmation" value="Confirm Password" />

                                            <TextInput
                                                id="password_confirmation"
                                                type="password"
                                                class="mt-1 block w-full"
                                                v-model="form.password_confirmation"
                                                required
                                                autocomplete="new-password"
                                            />

                                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                        </div>
                                    </div>

                                    <div class="w-full mt-4">
                                        <InputLabel for="id_picture" value="Valid ID" />

                                        <input type="file" id="id_picture" style="display: none"
                                                @change="idPictureChange($event)"
                                        />

                                        <img :src="form.id_picture != '' ? form.id_picture : '/images/upload_image.png'" alt="upload_image"
                                            class="cursor-pointer"
                                            @click="idPictureClick()"
                                            style="border: 1px solid black; border-radius: 5px; width: 100%; height: 220px;"
                                        >

                                        <InputError class="mt-2" :message="form.errors.id_picture" />
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <!-- <Link
                                            :href="route('login')"
                                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Already registered?
                                        </Link> -->

                                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Register
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="loginModal" class="loginModal md:mt-0">
                        <div class="login-modal-content flex flex-col" :style="{width: isMobileView ? '90%' : '35%'}">
                            <div class="w-full">
                                <span class="text-2xl font-bold ">
                                    LOGIN
                                </span>
                                <span class="float-right cursor-pointer"
                                    @click="closeLoginModal()"
                                >
                                    <i class="fa-solid fa-xmark"></i>
                                </span>
                            </div>

                            <div class="w-full mt-10">
                                <form @submit.prevent="login" >
                                    <div>
                                        <InputLabel for="email-login" value="Email" />

                                        <TextInput
                                            id="email-login"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="loginForm.email"
                                            required
                                            autofocus
                                            autocomplete="email"
                                        />

                                        <InputError class="mt-2" :message="form.errors.email" />
                                    </div>

                                    <div class="mt-4">
                                        <InputLabel for="password-login" value="Password" />

                                        <TextInput
                                            id="password-login"
                                            type="password"
                                            class="mt-1 block w-full"
                                            v-model="loginForm.password"
                                            required
                                            autocomplete="current-password"
                                        />

                                        <InputError class="mt-2" :message="form.errors.password" />
                                    </div>

                                    <div class="block mt-4">
                                        <label class="flex items-center">
                                            <Checkbox name="remember" v-model:checked="loginForm.remember" />
                                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Login
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

</template>

<style>
.main {
    height: 100%;
    min-height: 92vh;
    background-color: #E5E8E8;
}

.bg-landing-page {
    background-color: #EB984E;
}

.register-btn {
    background-color: #5499C7;
    width: 150px;
}

.center {
    display: flex;
    justify-content: center;
    align-items: center;
}

.registerModal {
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
.register-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 100%;
}

.loginModal {
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
.login-modal-content {
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
</style>
