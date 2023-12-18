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
                    owner_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    subscription: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    payment_method: { value: null, matchMode: FilterMatchMode.IN },
                    created_at: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                    status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                    amount: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
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
                    <p class="text-sm font-light">View Subscription History</p>
                    <div class="card mb-10 mt-2">
                        <DataTable v-model:filters="filters" :value="rows" tableStyle="min-width: 30rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                        :globalFilterFields="['owner_name', 'subscription', 'payment_method', 'status', 'amount', 'action']">
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
                            <Column field="owner_name" header="Owner Name" sortable style="min-width: 14rem" class="border-b">
                                <template #body="{ data }">
                                    {{ data.ownername }}
                                </template>
                            </Column>
                            <Column field="subscription" header="Subscription" sortable style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.subscription) }}
                                </template>
                            </Column>
                            <Column field="payment_method" header="Payment Method" sortable style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.paymentmethod) }}
                                </template>
                            </Column>
                            <Column field="status" header="Status" sortable style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.status) }}
                                </template>
                            </Column>
                            <Column field="amount" header="Amount" sortable style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.amount) }}
                                </template>
                            </Column>
                            <Column field="action" header="Action" sortable style="min-width: 10rem" class="border-b">
                                <template #body="{ data }">
                                    {{ formatDate(data.action) }}
                                    <AppDropdown class="flex justify-center items-center">
                                    <ConfirmDialog />
                                    <Toast />
                                    <button
                                    class="hover:text-orange-400"
                                    >
                                        <i  class="pi pi-ellipsis-h"></i>
                                    </button>
                                                <AppDropdownContent class="bg-white z-50 ">
                                                    <AppDropdownItem @click="noticeTermination(data)">
                                                        Warning 
                                                    </AppDropdownItem>
                                                   

                                                </AppDropdownContent>
                                    </AppDropdown>
                                </template>

                                
                            </Column>
                            
                        </DataTable>
                    </div>
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