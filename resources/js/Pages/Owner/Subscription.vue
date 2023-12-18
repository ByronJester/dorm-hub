<script>
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import DataTable from 'primevue/datatable';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { ref, onMounted, watch } from "vue";
export default{
    components:{
        SidebarLayout,
        DataTable,
        Button,
        Tag,
        Column,
        Row

    },
    setup(){
        const filters = ref();
        const getSeverity = (status) => {
                switch (status) {
                    case 'decline':
                        return 'danger';

                    case 'approved':
                        return 'success';

                    case 'pending':
                        return 'warning';

                }
            };

            const initFilters = () => {
                filters.value = {
                    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                    name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    user_type: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    phone_number: { value: null, matchMode: FilterMatchMode.IN },
                    created_at: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                    status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },

                };
            };

            initFilters();

            const clearFilter = () => {
                initFilters();
            };

            const formatDate = (value) => {
                // Check if value is a string and convert it to a Date object
                const date = typeof value === 'string' ? new Date(value) : value;

                // Check if date is a valid Date object
                if (isNaN(date.getTime())) {
                    // If not a valid Date, you can handle it according to your requirements
                    return "Invalid Date"; // or return value.toString() or any other representation
                }

                // If it's a valid Date object, format it
                return date.toLocaleDateString('en-US', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            };
        return{
            filters,
            getSeverity,
            clearFilter
        }
    }
}
</script>
<template>
    <SidebarLayout>
        <div class="px-4 pt-20 lg:ml-64">
            <div class="min-w-screen
                        mx-40">
            <p class="text-3xl font-black">Subscriptions</p>
            <div class="flex flex-row gap-20 mt-10 w-full">
                <div class="flex-grow">
                    <p class="text-lg font-black">Subsciption History</p>
                    <p class="text-sm font-light">View subscription history</p>
                    <div class="card mb-10 mt-2">
                        <DataTable v-model:filters="filters" :value="rows" tableStyle="min-width: 30rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                        :globalFilterFields="['name', 'phone_number', 'user_type', 'created_at', 'status']">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5" icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined @click="clearFilter()" />
                                <span class="p-input-icon-left">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input v-model="filters['global'].value" placeholder="Keyword Search" class="pl-10 rounded-lg" />
                                </span>
                            </div>
                        </template>
                        <template #empty> No subscription found. </template>
                            <Column field="name" header="Name" sortable style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ data.name }}
                                </template>
                            </Column>
                            <Column field="created_at" header="Date Subscribed" sortable dataType="date" style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.created_at) }}
                                </template>
                            </Column>
                            <Column field="created_at" header="Amount" sortable dataType="date" style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.created_at) }}
                                </template>
                            </Column>
                            <Column field="Action" header="Action" sortable style="min-width: 4rem" class="border-b">
                                <template #body="{ data }">
                                    <button v-if="data.is_paid" class="text-gray-400 disabled:cursor-not-allowed text-sm font-bold" disabled>Pay Now</button>
                                    <button v-if="!data.is_paid" class="text-gray-900 hover:text-orange-400 hover:underline text-sm font-bold"
                                        @click="payNow(data)"
                                    >
                                        Pay Now
                                    </button>
                                </template>
                            </Column>

                        </DataTable>
                    </div>
                </div>
                <div class="bg-orange-400 text-white rounded-lg w-64 h-64 px-10 py-8 shadow">
                    <p>Your Subscription</p>
                    <p class="mt-4 text-3xl font-extrabold ">Basic</p>
                    <p class=" text-md font-light">5 Dorm Listing</p>
                    <p class=" text-md font-medium">P1,000.00</p>
                    <button class="w-full border mt-3 rounded-lg py-2 hover:bg-white hover:text-orange-400">
                        Upgrade
                    </button>
                </div>
            </div>
        </div>
        </div>
    </SidebarLayout>
</template>

<style>
.p-datatable, .p-datatable-tbody, .p-datatable-emptymessage, .p-datatable-thead, .p-paginator{
    border: none;
    padding: 0;
    background-color: transparent;
}
</style>
