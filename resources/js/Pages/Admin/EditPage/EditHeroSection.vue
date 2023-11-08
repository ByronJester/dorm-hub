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
        const hero = page.props.hero;

        const bgImageClick = () => {
            document.getElementById("image_path").click();
        };

        const bgImageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                console.log(e);
                form.image_path = e.target.result;
            };
        };

        const back = () => {
            var url = null;

            if (user) {
                router.get(route("admin.maintenance"));
            } else {
                router.get(route("landing.page"));
            }
        };
        const form = useForm({
            title: hero.title,
            subtitle: hero.subtitle,
            description: hero.description,
            image_path: hero.image_path,
        });

        const update = () => {
            swal(
                {
                    title: "Are you sure to update your hero section?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    form.patch(route("hero.update"), {
                        preserveScroll: true,
                        onSuccess: () => {
                            swal(
                                "Success!",
                                "Your hero section has been updated.",
                                "success"
                            );
                            window.location.reload();
                        },
                    });
                }
            );
        };

        return {
            hero,
            form,
            back,
            update,
            bgImageClick,
            bgImageChange,
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
                    <p class="text-2xl font-semibold">Hero Section</p>
                    <p class="text-sm">Edit Hero Section</p>
                </div>
            </div>
            <form @submit.prevent="update()">
                <label
                    for="title"
                    class="block mb-2 text-sm font-medium text-gray-900"
                    >Heading:</label
                >

                <input
                    id="title"
                    type="text"
                    autocomplete="title"
                    class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2."
                    v-model="form.title"
                    required
                />

                <label
                    for="title"
                    class="block mb-2 text-sm font-medium text-gray-900"
                    >Sub Heading:</label
                >

                <input
                    id="title"
                    type="text"
                    autocomplete="sub_title"
                    class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                    v-model="form.subtitle"
                    required
                />

                <label
                    for="title"
                    class="block mb-2 text-sm font-medium text-gray-900"
                    >Description:</label
                >

                <input
                    id="title"
                    type="text"
                    autocomplete="sub_title"
                    class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                    v-model="form.description"
                    required
                />

                <label class="mt-2 mb-4"></label>
                <div class="mb-4">
                    <div class="mb-4">
                        <span class="text-md text-black"
                            >Background Image:</span
                        >
                    </div>

                    <input
                        type="file"
                        id="image_path"
                        class="hidden"
                        @change="bgImageChange($event)"
                        accept="image/*"
                    />

                    <label for="image_path" class="relative cursor-pointer">
                        <div
                            class="h-62 p-4 bg-gray-200 border w-full sm:w-2/4 md:w-2/5 lg:w-1/3 xl:2/4 2xl:w-3/12 border-dashed border-gray-400 rounded-lg"
                        >
                            <img
                                v-if="form.image_path"
                                :src="form.image_path"
                                alt="bg-image"
                                class="h-60 w-auto rounded-lg"
                            />
                            <span v-else>Select or Drag & Drop a file</span>
                        </div>
                    </label>

                    <InputError
                        class="mt-2"
                        :message="form.errors.image_path"
                    />
                </div>

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
