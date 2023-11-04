<script>
import Editor from "@tinymce/tinymce-vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthorizedLayout from "@/Layouts/SidebarLayout.vue";


export default {
    components: {
        Editor,
        AuthorizedLayout,
    },
    setup() {
        const page = usePage();

        const user = page.props.auth.user;

        const contactUs = page.props.contactUs;

        const form = useForm({
          email: contactUs.email,
          phone: contactUs.phone,
          facebook: contactUs.facebook,
          ig: contactUs.ig,
          address: contactUs.address,
        });

        const back = () => {
            var url = null;

            if (user) {
                router.get(route("admin.maintenance"));
            } else {
                router.get(route("landing.page"));
            }
        };
        

        const update = () => {
            swal(
                {
                    title: "Are you sure to update your Contact Us page?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    
                    form.patch(route("contactUs.update"), {
                        preserveScroll: true,
                        onSuccess: () => {
                            swal(
                                "Success!",
                                "Contact Us page has been updated.",
                                "success"
                            );
                            window.location.reload();
                        },
                    });
                }
            );
        };

        return {
            form,
            contactUs,
            back,
            update,
        };
    },
};
</script>

<template>
    <AuthorizedLayout>
        <div class="p-4 mt-16 lg:ml-64">
            <div class="flex flex-row gap-2 items-center">
                <button
                    @click="back()"
                    class="border-2 border-gray-500 px-3 py-1 text-gray-500 hover:text-white hover:border-orange-400 rounded-md hover:bg-orange-400"
                >
                    <span>
                        <i class="fa-solid fa-arrow-left fa-lg"></i>
                    </span>
                </button>
                <div>
                    <p class="text-2xl font-semibold">Contact Us</p>
                    <p class="text-sm">Edit Contact Us page</p>
                </div>
            </div>
            <form class="mx-20" @submit.prevent="update()">
                <div class="grid grid-cols lg:grid-cols-2 mt-12 gap-2">
                    <div>
                        <label
                            for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 "
                            >Email:</label
                        >

                        <input
                            id="email"
                            type="text"
                            v-model="form.email"
                            class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                            required
                        />

                        <label
                            for="phone"
                            class="block mb-2 text-sm font-medium text-gray-900 "
                            >Phone:</label
                        >

                        <input
                            id="phone"
                            type="text"
                            v-model="form.phone"
                            class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                            required
                        />
                    </div>
                    <div>
                        <label
                            for="facebook"
                            class="block mb-2 text-sm font-medium text-gray-900 "
                            >Facebook:</label
                        >

                        <input
                            id="facebook"
                            type="text"
                            v-model="form.facebook"
                            class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                            required
                        />

                        <label
                            for="ig"
                            class="block mb-2 text-sm font-medium text-gray-900 "
                            >Ig:</label
                        >

                        <input
                            id="phone"
                            type="text"
                            v-model="form.ig"
                            class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                            required
                        />
                    </div>
                </div>
                <label
                    for="phone"
                    class="block mb-2 text-sm font-medium text-gray-900 "
                    >Address:</label
                >
                <input
                    id="selectedLocation"
                    type="text"
                    v-model="form.address"
                    autocomplete="address"
                    class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5"
                />
                <button
                    type="submit"
                    class="bg-orange-400 mt-4 text-white px-4 py-2 rounded-lg"
                >
                    Save
                </button>
            </form>
        </div>
    </AuthorizedLayout>
</template>
