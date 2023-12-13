<script >
import Rooms from '@/Components/RoomCard.vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { usePage, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue'
export default{
    components:{
        Rooms,
        SidebarLayout,
        GuestLayout
    },
    setup(){
        const page = usePage()
        const room = page.props.dorm;
        const user = page.props.user;

        const back = () => {
            var id = room.id;
            router.get(route('view.dorm', id));
        }

        const availableRooms = room.rooms.filter(x => { return !!x.is_available })
        console.log(availableRooms.length);
        return{
            room,
            user,
            back,
            availableRooms
        }
    }
}

</script>
<template>
    <SidebarLayout v-if="user">
        <div class="p-4  lg:ml-64">
            <p class="cursor-pointer mt-20 " @click="back()">
                <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
            </p>
            <p class="text-4xl  font-bold">Rooms</p>
            <hr class="mt-3"/>
            <div v-if="availableRooms.length < 1" class="w-full text-center bg-gray-400 py-2 text-white ">No Rooms Avaialble</div>
            <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 mx-3 mt-5 gap-2">
                <Rooms :dorms.sync="room" :user.sync="user"/>
            </div>
        </div>
    </SidebarLayout>
    <GuestLayout v-else>
        <div class="my-20 mx-40">
            <p class="text-4xl mb-5 font-bold">Rooms</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 mx-3 gap-2">
                <Rooms :dorms.sync="room" :user.sync="user"/>
            </div>
        </div>
        
    </GuestLayout>
    
</template>