<script setup>
import { onMounted, ref } from 'vue';

let tickets = ref([]);

onMounted(async () => {
    getTickets();
});

const getTickets = async () => {
let res = await axios.get('/api/get_all_tickets')
console.log(res)
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
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
