<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

let tickets = ref([]);
let ticket_links = ref([]);
let currentPage = ref(1);

const itemsPerPage = 10;

const getTickets = async () => {

    const res = await axios.get(`/api/get_all_ticket?page=${currentPage.value}&per_page=${itemsPerPage}`, { headers: { 'Accept': 'application/json' } });
    tickets.value = res.data.data;
    currentPage.value = res.data.current_page
    ticket_links.value = res.data.links
};

// Search bar and filter
let searchTerm = ref('');

const filteredTickets = computed(() => {

    return tickets.value.filter((ticket) =>
        ticket.title.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const decodeHtml = ((text) => {
    const parser = new DOMParser();
    const decoded = parser.parseFromString(text, 'text/html').body.textContent;
    return decoded
})

const handlePageChange = (link) => {
    let page = currentPage.value;
    if (link.url === null) return
    if (link.label.indexOf('&laquo;') > -1) {
        page -= 1
    }
    else if (link.label.indexOf('&raquo;') > -1) {
        page += 1
    }
    else {
        page = link.label
    }
    currentPage.value = page
    getTickets()
}

// Fetch data on mount
onMounted(async () => {
    await getTickets();
});
</script>

<template>
    <div class="p-4">
        <!-- Search bar -->
        <div class="mb-4">
            <input v-model="searchTerm" type="text" class="border rounded py-2 px-3 w-full"
                placeholder="Search tickets by title..." />
        </div>

        <!-- Table -->
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 w-1">Priority</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2 w-1">Status</th>
                    <th class="px-4 py-2 w-40">Date</th>
                </tr>
            </thead>
            <tbody v-if="tickets.length > 0">
                <tr v-for="ticket in tickets" :key="ticket.id">
                    <td class="px-4 py-2 text-center">
                        {{ ticket.priority }}</td>
                    <td class="px-4 py-2 text-left">{{ ticket.title }}</td>
                    <td class="px-4 py-2 text-center">
                        <span
                            class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                            {{ ticket.status }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center">{{ ticket.human_readable_created_at }}</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td class="px-4 py-2" :colspan="5">Ticket not found</td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            <div v-for="link in ticket_links" :key="link.id">
                <button class="mx-1 py-1 px-3 rounded " :class="{
                    'hover:bg-blue-300 bg-blue-500 text-white': !link.active,
                    'bg-blue-700 text-gray-300 cursor-not-allowed': link.active
                }" :disabled="link.active" @click="handlePageChange(link)">
                    {{ decodeHtml(link.label) }}
                </button>
            </div>
        </div>
    </div>
</template>



<style></style>
