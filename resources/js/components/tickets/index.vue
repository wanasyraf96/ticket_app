<script setup>
import { onMounted, ref } from 'vue';

let tickets = ref([]);

onMounted(async () => {
    getTickets();
});

const getTickets = async () => {
    const res = await axios.get('/api/get_all_ticket')
    console.log(res)
    tickets.value = res.data.data
};

</script>

<template>
    <div class="">

        <!-- table -->
        <table class="">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ticket in tickets" :key="'ticket.id'" v-if="tickets.length > 0">
                    <td>{{ ticket.status }}</td>
                    <td>{{ ticket.title }}</td>
                    <td>{{ ticket.created_at }}</td>
                    <td>{{ ticket.priority }}</td>
                </tr>
                <tr v-else>
                    <td>Ticket not found</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
