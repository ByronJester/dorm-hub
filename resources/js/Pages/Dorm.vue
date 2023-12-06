<script>
    import TenantLayout from '@/Layouts/AuthenticatedLayout.vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import OtherLayout from '@/Layouts/SidebarLayout.vue';
    import { usePage, Head, Link, router, useForm  } from '@inertiajs/vue3'
    import Dorm from '@/Components/Dorm.vue';
    import { ref } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import Checkbox from '@/Components/Checkbox.vue';

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
            GuestLayout,
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

            const application = null

            return {
                user,
                dorm,
                application,
                showingNavigationDropdown,
                isMobileView,
                form,
                loginForm,
                idPictureClick,
                idPictureChange,
                submit,
                back,
                openRegisterModal,
                closeRegisterModal,
                openLoginModal,
                closeLoginModal,
                login,
                closeTermsModal,
                openTutModal,
                closeTutModal,
                selectUserType,
                openTermsModal,
                SelfieidPictureChange,
                SelfieidPictureClick

            }
        }
    }
</script>


<template>
    <div>
        <div class="w-full" v-if="user">
            <OtherLayout v-if="user.user_type == 'tenant'">
                <div class="p-4 lg:ml-64">
                    <div
                        className="
                        max-w-screen-lg
                        mx-auto
                        "
                    >
                    <div class="w-full pt-24 pl-5">
                        <p class="cursor-pointer" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>

                    <div class="w-full mt-5">
                        <Dorm :dorm.sync="dorm" :user.sync="user" :application.sync="application" ></Dorm>
                    </div>
                    </div>
                </div>
                    
            </OtherLayout>


            <OtherLayout v-else>
                <div class="p-4 lg:ml-64">
                    <div
                        className="
                        max-w-screen-lg
                        mx-auto
                        "
                    >
                    <div class="w-full pt-24 pl-5">
                        <p class="cursor-pointer" @click="back()">
                            <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                        </p>
                    </div>

                    <div class="w-full mt-5">
                        <Dorm :dorm.sync="dorm" :user.sync="user" :application.sync="application"></Dorm>
                    </div>
                </div>
                </div>
            </OtherLayout>
        </div>


        <div class="bg-white w-full flex flex-col" style="min-height: 100vh;" v-else>
            <GuestLayout>
                <div class="max-w-[2520px] xl:px-20 md:px-10 sm:px-2 px-4">
                <div class="w-full pt-24 pl-5">
                    <p class="cursor-pointer" @click="back()">
                        <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                    </p>
                </div>
                <Dorm :dorm.sync="dorm" :user.sync="user" :application.sync="application"></Dorm>
            </div>
            </GuestLayout>
        </div>
    </div>
</template>

