<script>
import Editor from "@tinymce/tinymce-vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import AuthorizedLayout from "@/Layouts/SidebarLayout.vue";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

export default {
    components: {
        Editor,
        AuthorizedLayout,
        ConfirmDialog,
        Toast
    },
    setup() {
        const page = usePage();
        const confirm = useConfirm();
        const toast = useToast();
        const user = page.props.auth.user;
        const aboutUs = page.props.aboutUs;

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
            title: aboutUs.title,
            sub_title: aboutUs.sub_title,
            content: aboutUs.content,
            image_path: aboutUs.image_path,
        });

        const update = () => {
            confirm.require({
                message: 'Are you sure you want to update this about us?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    form.patch(route("aboutUs.update"), {
                        preserveScroll: true,
                        onSuccess: () => {
                            toast.add({ severity: 'info', summary: 'Success', detail: 'Successfully update about us', life: 3000 });
                            window.location.reload();
                        },
                    });
                },
                reject: () =>{

                }
            });
        };

        return {
            aboutUs,
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
                <p class="text-2xl font-semibold my-4">About Us</p>
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
                    class="bg-gray-50 mb-4 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
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
                    v-model="form.sub_title"
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

                    <label for="image_path" class="block cursor-pointer">
                        <div
                            class="h-62 p-4 bg-gray-200 border w-full sm:w-2/4 md:w-2/5 lg:w-1/3 xl:2/4 2xl:w-3/12 border-dashed border-gray-400 rounded-lg"
                        >
                            <img
                                v-if="form.image_path"
                                :src="form.image_path"
                                alt="bg-image"
                                class="h-62 w-auto rounded-lg"
                            />
                            <span v-else>Select or Drag & Drop a file</span>
                        </div>
                    </label>

                    <InputError
                        class="mt-2"
                        :message="form.errors.image_path"
                    />
                </div>
                <div>
                    <div class="text-md text-black mb-4">Content:</div>
                    <Editor
                        id="content"
                        api-key="rnwni8gfoofnq592kqlityphztlce2nvzunwxpqqs3a0y8dv"
                        v-model="form.content"
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
                </div>
                <ConfirmDialog />
                <Toast />
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
