<script>
import Editor from "@tinymce/tinymce-vue";
import { useForm, usePage, router} from "@inertiajs/vue3";
import { ref } from "vue";
import AuthorizedLayout from '@/Layouts/SidebarLayout.vue'
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

        const user = page.props.auth.user;
        const policy = page.props.policy;
        const toast = useToast();
        const confirm = useConfirm();
        const back = () => {
            var url = null;

            if (user) {
                router.get(route("admin.maintenance"));
            } else {
                router.get(route("landing.page"));
            }
        };
        const form = useForm({
          content: policy.content
        });

        const update = () => {
          confirm.require({
                message: 'Are you sure you want to update this privacy policy?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
            accept: () => {
                    
            form.patch(route("policy.update"), {
                preserveScroll: true,
                onSuccess: () => {
                  toast.add({ severity: 'info', summary: 'Success', detail: 'Successfully update privacy policy', life: 3000 });
                    window.location.reload();
                },
            });
        },reject: () =>{

        }
      });
};  
          
          
     return{
            policy,
            form,
            back,
            update
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
        <p class="text-2xl font-semibold">Privacy Policy</p>
        <p class="text-sm">Edit Privacy Policy page</p></div>
    </div>
    <form @submit.prevent="update()">  
      <label
        for="title"
        class="block mb-2 text-xl font-bold mt-6 text-gray-900"
      >Content:</label>

      <div>
        <Editor
          id="content"
          api-key="rnwni8gfoofnq592kqlityphztlce2nvzunwxpqqs3a0y8dv"
          v-model="form.content"
          :init="{
            menubar: false,
            plugins: 'lists link image emoticons',
            toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
          }"
        />
      </div>
      
      <ConfirmDialog />
                <Toast />
      <button type="submit" class="bg-blue-500 mt-4 text-white px-4 py-2 rounded-lg">Save</button>
    </form>
  </div>
  </AuthorizedLayout>
</template>
