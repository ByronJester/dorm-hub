<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    first_name: user.first_name,
    middle_name: user.middle_name,
    last_name: user.last_name,
    phone_number: user.phone_number,
    email: user.email,
    image: user.image,
    bio: user.bio,
    pk: user.pk,
    sk: user.sk
});

const updateProfile = () => {
    swal({
        title: "Are you sure to update your profile?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    },
    function(){
        form.patch(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                swal("Success!", "Your profile has been updated.", "success");
            },
            onError: () => {
                if (form.errors.password) {
                    form.reset('password', 'password_confirmation');
                    passwordInput.value.focus();
                }
                if (form.errors.current_password) {
                    form.reset('current_password');
                    currentPasswordInput.value.focus();
                }
            },
        });
    });
}

const imageClick = () => {
    document.getElementById('profile_image').click()
}

const imageChange = (e) => {
    const image = e.target.files[0];

    const reader = new FileReader();

    reader.readAsDataURL(image);

    reader.onload = e =>{
        form.image = e.target.result
    }
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
            <div class="w-full flex flex-col md:flex-row">
                <div class="w-full md:w-8/12">
                    <div class="flex flex-col md:flex-row w-full">
                        <div class="w-full md:mr-1">
                            <InputLabel for="first_name" value="First Name" />

                            <TextInput
                                id="first_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.first_name"
                                required
                                autocomplete="first_name"
                            />

                            <InputError class="mt-2" :message="form.errors.first_name" />
                        </div>

                        <div class="w-full md:mr-1">
                            <InputLabel for="middle_name" value="Middle Name" />

                            <TextInput
                                id="middle_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.middle_name"
                                autocomplete="middle_name"
                            />

                            <InputError class="mt-2" :message="form.errors.middle_name" />
                        </div>

                        <div class="w-full">
                            <InputLabel for="last_name" value="Middle Name" />

                            <TextInput
                                id="last_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.last_name"
                                required
                                autocomplete="last_name"
                            />

                            <InputError class="mt-2" :message="form.errors.last_name" />
                        </div>
                    </div>

                    <div class="w-full flex flex-col md:flex-row md:mt-5">
                        <div class="w-full md:mr-1">
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
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                    </div>

                    <div class="w-full mt-2">
                        <InputLabel for="bio" value="Bio" />

                        <textarea rows="4" cols="50" class="w-full text-sm" v-model="form.bio">

                        </textarea>
                    </div>

                    <div class="w-full flex flex-col md:flex-row md:mt-5" v-if="user.user_type == 'owner'">
                        <div class="w-full md:mr-1">
                            <InputLabel for="pk" value="Paymongo Public Key" />

                            <TextInput
                                id="pk"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.pk"
                            />

                            <InputError class="mt-2" :message="form.errors.pk" />
                        </div>

                        <div class="w-full">
                            <InputLabel for="sk" value="Paymongo Secret Key" />

                            <TextInput
                                id="sk"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.sk"
                            />

                            <InputError class="mt-2" :message="form.errors.sk" />
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-4/12 mt-5 md:mt-0">
                    <div class="w-full md:px-10">
                        <InputLabel for="profile_picture" value="Profile Picture" />

                        <input type="file" id="profile_image" style="display: none"
                                @change="imageChange($event)"
                        />

                        <img :src="form.image ?? '/images/upload_image.png'"
                            class="cursor-pointer"
                            @click="imageClick()"
                            style="border: 1px solid black; border-radius: 5px; width: 100%; height: 260px;"
                        >
                    </div>
                </div>

            </div>


            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
