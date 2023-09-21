<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';

    export default {
        components: {
            AuthenticatedLayout,
            VueGoodTable
        },
        setup() {
            const isMobileView = ref(false)
            const page = usePage()
            const rows = ref([])
            const columns = ref([
                {
                    label: 'Name',
                    field: 'name',
                },
                {
                    label: 'Contact Number',
                    field: 'phone_number',
                },
                {
                    label: 'Email',
                    field: 'email',
                },
                {
                    label: 'Status',
                    field: 'status',
                },
                {
                    label: 'Actions',
                    field: 'action',
                },

            ])

            onMounted(() => {
                rows.value = page.props.users
            });

            const id_picture = ref(null)
            const user = ref(null)

            const openModal = (arg) => {
                id_picture.value = arg.id_picture
                user.value = arg

                var modal = document.getElementById("userModal");

                modal.style.display = "block";

            }

            const closeModal = () => {
                var modal = document.getElementById("userModal");

                modal.style.display = "none";
            }

            const changeStatus = (id, status) => {
                swal({
                    title: `Are you sure to ${!status ? 'deny' : 'approve'} the rental status of this tenant?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('tenant.change.status', status), {id: id, status: status})
                        .then(response => {
                            swal("Success!", `You successfully ${!status ? 'denied' : 'approved'} the rental rental status of this tenant.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 1500);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });


            }


            return {
                isMobileView,
                columns,
                rows,
                id_picture,
                user,
                openModal,
                closeModal,
                changeStatus
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            <div class="w-full pt-2 md:pt-20">
                <vue-good-table
                    styleClass="vgt-table condensed"
                    style="padding: 0vw 5vw; width: 100%"
                    :columns="columns"
                    :rows="rows"
                    :pagination-options="{ enabled: true }"
                    :select-options="{ enabled: false }"
                    :search-options="{ enabled: true }"
                >
                    <template #table-row="props">
                        <div v-if="props.column.field == 'status'" class="mt-2">
                            <button class="bg-orange-500 p-1 mx-1 text-white rounded-md text-xs"
                                v-if="!props.row.is_approved"
                                :disabled="true"
                            >
                                PENDING
                            </button>

                            <button class="bg-cyan-900 p-1 mx-1 text-white rounded-md text-xs"
                                v-if="!!props.row.is_approved"
                                :disabled="true"
                            >
                                APPROVED
                            </button>
                        </div>

                        <div v-if="props.column.field == 'action'">
                            <button class="bg-cyan-500 p-3 mx-1 text-white rounded-md text-xs"
                                @click="openModal(props.row)"
                                :disabled="!!props.row.is_approved"
                                :class="{'cursor-not-allowed' : !!props.row.is_approved}"
                            >
                                View
                            </button>
                        </div>
                    </template>
                </vue-good-table>
            </div>

            <div class="w-full">
                <div id="userModal" class="userModal mt-10 md:mt-0">
                    <div class="user-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Valid ID
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5" v-if="id_picture">
                            <img :src="id_picture" alt="id_picture"
                                style="width: 100%; height: 250px;"
                            >
                        </div>

                        <div class="w-full mt-5" v-if="user">
                            <button class="bg-cyan-500 p-3 mx-1 text-white rounded-md text-xs float-right"
                                v-if="!user.is_approved"
                                @click="changeStatus(user.id, true)"
                            >
                                Approve
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }

    .userModal {
        display: none;
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
    .user-modal-content {
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
