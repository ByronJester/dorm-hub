<script>
    import TenantLayout from '@/Layouts/AuthenticatedLayout.vue';
    import OtherLayout from '@/Layouts/SidebarLayout.vue';
    import { usePage, Head, Link, router, useForm  } from '@inertiajs/vue3'
    import Dorm from '@/Components/Dorm.vue';
    import { ref, onMounted } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import Checkbox from '@/Components/Checkbox.vue';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';

    export default {
        components: {
            Dorm,
            ApplicationLogo,
            Link,
            InputLabel,
            PrimaryButton,
            TextInput,
            InputError,
            Checkbox,
            TenantLayout,
            OtherLayout,
            VueGoodTable
        },
        setup() {
            const page = usePage()

            const showingNavigationDropdown = ref(false);
            const user = page.props.auth.user;
            const dorm = page.props.dorm;

            const back = () => {
                var url = null;

                if(user) {
                    url = user.user_type + '.dorms'
                } else {
                    url = 'landing.page'
                }

                router.get(route(url));
            }

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

            const isMobileView = ref(false)

            isMobileView.value = screen.width < 600;

            const form = useForm({
                first_name: '',
                middle_name: '',
                last_name: '',
                phone_number: '',
                email: '',
                user_type: 'tenant',
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

            const roomTenants = ref([]);

            const getRoomDetails = async(month = '1') => {
                await axios.get(`/owner/room-details/${month}/${dorm.id}`)
                    .then(response => {
                        roomTenants.value = response.data.roomTenants
                    })
                    .catch(error => {
                        errors.value = error.response.data.errors
                        loading.value = false
                    })
            }

            onMounted(() => {
                // getRoomDetails()
            });


            const dateChange = (e) => {
                getRoomDetails(e.target.value)
            }

            const columns = [
                {
                    label: 'Room',
                    field: 'room_name',
                },
                {
                    label: 'Tenant',
                    field: 'tenant_name',
                },
                {
                    label: 'Fee',
                    field: 'monthly_price',
                },
                {
                    label: 'Availability',
                    field: 'availability',
                },
                {
                    label: 'Status',
                    field: 'status',
                },
                {
                    label: 'Actions',
                    field: 'action',
                }
            ]


            return {
                user,
                dorm,
                showingNavigationDropdown,
                isMobileView,
                form,
                loginForm,
                columns,
                roomTenants,
                idPictureClick,
                idPictureChange,
                submit,
                back,
                openRegisterModal,
                closeRegisterModal,
                openLoginModal,
                closeLoginModal,
                login,
                dateChange
            }
        }
    }
</script>


<template>
    <div>
        <div class="w-full" v-if="user">
            <TenantLayout v-if="user.user_type == 'tenant'">
                <div class="main w-full flex flex-col" style="min-height: 92vh;">
                    <div class="w-full mt-5 pl-5">
                        <p class="cursor-pointer" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>

                    <div class="w-full mt-5">
                        <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
                    </div>
                </div>
            </TenantLayout>


            <OtherLayout v-else>
                <div class="main w-full flex flex-col" style="min-height: 92vh;">
                    <div class="w-full mt-5 pl-5">
                        <p class="cursor-pointer" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>

                    <!-- <div class="w-full mt-10">
                        <div class="w-full" :style="{ padding: !isMobileView ? '0vw 17vw' : '0vw 5vw'}">
                            <select class="float-right rounded-md" @change="dateChange($event)">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>

                        <div class="w-full mt-12" :style="{ padding: !isMobileView ? '0vw 17vw' : '0vw 5vw'}">
                            <vue-good-table
                                styleClass="vgt-table condensed"
                                style="width: 100%"
                                :columns="columns"
                                :rows="roomTenants"
                                :pagination-options="{ enabled: true }"
                                :select-options="{ enabled: false }"
                                :search-options="{ enabled: true }"
                            >
                                <template #table-row="props">
                                    <div v-if="props.column.field == 'availability'" class="mt-2">
                                        <button class="bg-cyan-500 p-1 mx-1 text-white rounded-sm text-xs"
                                            :disabled="true"
                                        >
                                            {{props.row.availability ? 'Available' : 'Not Available'}}
                                        </button>
                                    </div>

                                    <div v-if="props.column.field == 'status'" class="mt-2">
                                        <button class="bg-cyan-500 p-1 mx-1 text-white rounded-sm text-xs"
                                            :disabled="true"
                                        >
                                            {{props.row.status}}
                                        </button>
                                    </div>

                                    <div v-if="props.column.field == 'action'">
                                        <button class="bg-orange-500 p-3 mx-1 text-white rounded-md text-xs" >
                                            Manage Availability
                                        </button>
                                    </div>
                                </template>
                            </vue-good-table>
                        </div>

                    </div> -->

                    <div class="w-full mt-20">
                        <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
                    </div>
                </div>
            </OtherLayout>
        </div>


        <div class="main w-full flex flex-col" style="min-height: 100vh;" v-else>
            <nav class="bg-white border-b border-gray-300 py-1">
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
                                    class="ml-4 font-semibold text-black mt-5 cursor-pointer"
                                    >LOGIN
                                </span>

                                <span
                                    @click="openRegisterModal()"
                                    class="ml-4 font-semibold text-black mt-5 cursor-pointer"
                                    >SIGN UP</span
                                >
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

            <div class="w-full">
                <div id="registerModal" class="registerModal mt-40 md:mt-0">
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
            <div class="w-full mt-8 pl-5">
                <p class="cursor-pointer" @click="back()">
                    <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                </p>
            </div>

            <div class="w-full mt-10">
                <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
            </div>
        </div>
    </div>
</template>

<style>
    .main {
        height: 100%;
        background-color: #E5E8E8;
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
