<script setup>
import { ref, computed, onMounted, watch, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { ChevronDownIcon as HeroIconChevronDown, ChevronUpIcon as HeroIconChevronUp } from '@vue-hero-icons/solid';

const router = new useRouter()
let tickets = ref([]);
let ticket_links = ref([]);
let currentPage = ref(1);
let searchTerm = ref(null)
let search = ref(null)
let searchTimeout
let numRows = ref(10);
let priorities = ref([])
let statuses = ref([])
let showModal = ref(false);
let selectedStatus = ref('');
let selectedPriority = ref('');
let filter = ref('')

const tableHeaders = reactive([
    { label: 'No.', key: 'number', size: 16, isSortable: false },
    { label: 'Assign To', key: 'assign', size: 10, isSortable: false },
    { label: 'Title', key: 'title', size: 0, isSortable: true },
    { label: 'Priority', key: 'priority', size: 26, isSortable: true },
    { label: 'Status', key: 'status', size: 24, isSortable: true },
    { label: 'Date', key: 'date', size: 36, isSortable: true },
]);

// Single Sort
// const sortKey = ref(null)
// const isAscending = ref(true)
// const sort = (key) => {
//     if (sortKey.value === key) {
//         isAscending.value = !isAscending.value;
//     } else {
//         sortKey.value = key;
//         isAscending.value = true;
//     }
// }

// Multiple Sort
const sortKeys = ref([]);
const isAscending = reactive({});

const sort = (key) => {
    if (sortKeys.value.includes(key)) {
        isAscending[ key ] = !isAscending[ key ];
    } else {
        sortKeys.value.push(key);
        isAscending[ key ] = true;
    }

    // Implement your sorting logic here based on sortKeys.value and isAscending
    // For example, you can sort the table data using a computed property.
};
const isSorted = (key) => {
    return sortKeys.value.includes(key);
};


const prioritiesColor = [
    { id: 1, name: 'low', colorClass: 'bg-blue-200 text-blue-800' },
    { id: 2, name: 'medium', colorClass: 'bg-yellow-200 text-yellow-800' },
    { id: 3, name: 'high', colorClass: 'bg-orange-200 text-orange-800' },
    { id: 4, name: 'urgent', colorClass: 'bg-red-200 text-red-800' },
];

const statusesColor = [
    { id: 1, name: 'active', colorClass: 'bg-green-200 text-green-800' },
    { id: 2, name: 'deactive', colorClass: 'bg-gray-200 text-gray-800' },
    { id: 3, name: 'complete', colorClass: 'bg-teal-200 text-teal-800' },
    { id: 4, name: 'pending', colorClass: 'bg-yellow-200 text-yellow-800' },
];

const numRowsOptions = [ 10, 25, 50, 100 ];

const getTickets = async () => {
    if (searchTerm.value) {
        search.value = `&search=${encodeURIComponent(searchTerm.value.trim().toLowerCase())}`
    }

    const url = `/api/get_all_ticket?page=${currentPage.value}&per_page=${numRows.value}${search.value === null ? '' : search.value}${filter.value === null ? '' : filter.value}`
    const res = await axios.get(url, { headers: { 'Accept': 'application/json' } });
    tickets.value = res.data.data;
    currentPage.value = res.data.current_page
    ticket_links.value = res.data.links
};

const getLookup = async () => {
    const res = await axios.get('/api/lookup')
    res.data.forEach(element => {
        if (element.for.match('ticket_priority')) {
            priorities.value = element.data
        }
        if (element.for.match('ticket_status')) {
            statuses.value = element.data
        }
    });
}

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

const handleSearch = () => {
    // Clear any previous timeouts to avoid multiple executions
    clearTimeout(searchTimeout);

    // Set a new timeout to delay the execution of getTickets()
    searchTimeout = setTimeout(() => {
        getTickets();
    }, 500); // Adjust the delay time as needed (in milliseconds)
}
const handleSearchKeyUp = (event) => {
    if (event.target.value === '' || event.target.value === null) {
        search.value = null
    }
    handleSearch()
}
const handleTicketView = (ticket) => {
    router.push(ticket.link);
}

const getStatusColor = ((input) => {
    const statusColor = statusesColor.find((item) => item.name === input)
    if (statusColor) {
        return statusColor.colorClass;
    } else {
        // Default color class if the ticket status is not found in the statusesColor array
        return 'bg-gray-200 text-gray-800';
    }
})

const getPriorityColor = ((input) => {
    const priorityColor = prioritiesColor.find((item) => item.name === input)
    if (priorityColor) {
        return priorityColor.colorClass;
    } else {
        // Default color class if the ticket status is not found in the statusesColor array
        return 'bg-gray-200 text-gray-800';
    }
})

const showFilterModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const applyFilter = () => {
    let filterOpt = '&filter='

    if (selectedStatus.value !== '') {
        filterOpt = filterOpt + encodeURIComponent('status=' + btoa(selectedStatus.value)) + ','
    }
    if (selectedPriority.value !== '') {
        filterOpt = filterOpt + encodeURIComponent('priority=' + btoa(selectedPriority.value))
    }
    filter.value = filterOpt
    currentPage.value = 1
    getTickets();

    closeModal(); // Close the modal after applying the filter
};

const resetFilter = () => {
    selectedStatus.value = '';
    selectedPriority.value = '';
};

const capitalizeWord = (str) => {
    if (typeof str !== "string" || str.length === 0) {
        return str;
    }
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// watch number of rows change
watch(numRows, () => {
    getTickets()

})

// Fetch data on mount
onMounted(async () => {
    await getTickets();
    await getLookup()
});
</script>

<template>
    <div class="flex flex-col justify-center items-center min-h-screen bg-slate-500">
        <div class="p-4 w-full max-w-4xl">
            <div class="overflow-x-auto overflow-y-auto h-full bg-white pb-4 border-2 rounded">
                <div class="flex">
                    <!-- Search bar -->
                    <div class="m-4 flex-grow">
                        <input v-model="searchTerm" @keyup="handleSearchKeyUp" type="text"
                            class="border rounded py-2 px-3 w-full" placeholder="Search tickets by title..." />
                    </div>

                    <!-- modal popup to filter -->
                    <div class="m-4">
                        <!-- Button to trigger the modal -->
                        <button @click="showFilterModal"
                            class="text-sm bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>

                        <!-- The modal -->
                        <div v-if="showModal"
                            class="fixed inset-0 flex items-center justify-center z-10 bg-black bg-opacity-50">
                            <div class="bg-white p-6 rounded">
                                <h2 class="text-lg font-semibold mb-4">Filter Options</h2>
                                <!-- Add your filtering options here, for example: -->
                                <div class="mb-4">
                                    <label for="status">Status:</label>
                                    <select v-model="selectedStatus" id="status"
                                        class="text-sm border rounded py-2 px-3 focus:outline-none">
                                        <option value="">All</option>
                                        <option v-for="item in statuses" :key="item.id" :value="item.id">
                                            {{ capitalizeWord(item.name) }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="status">Priority:</label>
                                    <select v-model="selectedPriority" id="status"
                                        class="text-sm border rounded py-2 px-3 focus:outline-none">
                                        <option value="">All</option>
                                        <option v-for="item in priorities" :key="item.id" :value="item.id">
                                            {{ capitalizeWord(item.name) }}
                                        </option>
                                    </select>
                                </div>


                                <!-- Add more filter options as needed -->
                                <div>
                                    <button @click="applyFilter"
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                        Apply
                                    </button>
                                    <button @click="resetFilter"
                                        class="ml-2 text-gray-500 hover:text-gray-600 font-bold py-2 px-4 rounded">
                                        Reset
                                    </button>
                                    <button @click="closeModal"
                                        class="ml-2 text-gray-500 hover:text-gray-600 font-bold py-2 px-4 rounded">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Rows Dropdown -->
                    <div class="m-4">
                        <label for="numRows">Number of Rows:</label>
                        <select v-model="numRows" id="numRows" class="text-sm border rounded py-2 px-3 focus:outline-none">
                            <option v-for="option in numRowsOptions" :value="option" :key="option">
                                {{ option }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <table class="table-auto w-full border-collapse">
                    <!-- <thead class="">
                        <tr class="border-b">
                            <th class="px-4 py-4 text-center w-32">
                                No.
                            </th>
                            <th class="px-4 py-4 text-center w-24">
                                Priority
                            </th>
                            <th class="px-4 py-4 text-center">
                                Title
                            </th>
                            <th class="px-4 py-4 text-center w-24">
                                Status
                            </th>
                            <th class="px-4 py-4 text-center w-32">
                                Date
                            </th>
                        </tr>
                    </thead> -->

                    <!-- Single Sorting -->
                    <!-- <thead>
                        <tr class="border-b">
                            <th v-for="header in tableHeaders" :key="header.key" :class="{
                                'sorted-asc': sortKey === header.key && isAscending,
                                'sorted-desc': sortKey === header.key && !isAscending,
                                'px-4 py-4 text-center w-24 sortable-header': header.isSortable,
                                'px-4 py-4 text-center': !header.isSortable,
                            }" @click="header.isSortable && sort(header.key)">
                                {{ header.label }}
                            </th>
                        </tr>
                    </thead> -->

                    <thead>
                        <tr class="border-b">
                            <th v-for="header in tableHeaders" :key="header.key" :class="{
                                'sorted-asc': isSorted(header.key) && isAscending[header.key],
                                'sorted-desc': isSorted(header.key) && !isAscending[header.key],
                                'px-4 py-4 text-center sortable-header': header.isSortable,
                                'px-4 py-4 text-center ': !header.isSortable,
                                ['w-' + header.size]: header.size
                            }" @click="header.isSortable && sort(header.key)">
                                {{ header.label }}
                            </th>
                        </tr>
                    </thead>

                    <tbody v-if="tickets.length > 0">
                        <tr v-for="(ticket, index) in tickets" :key="ticket.id"
                            class="hover:bg-gray-200 cursor-pointer border-b border-slate-200"
                            @click="handleTicketView(ticket)">
                            <td class="px-4 py-2 text-center">{{ index + 1 + ((currentPage - 1) * numRows) }}</td>

                            <td class="px-4 py-2 text-left">
                                <div class="overflow-hidden max-w-xs">
                                    {{ ticket.user.name }}
                                </div>
                            </td>
                            <td class="px-4 py-2 text-left">
                                <div class="overflow-hidden max-w-xs">
                                    {{ ticket.title }}
                                    <span class="text-xs font-bold">
                                        {{ ` (#${ticket.id})` }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-2 text-center">
                                <span
                                    :class="`bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ${getPriorityColor(ticket.priority)}`">
                                    {{ ticket.priority }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span
                                    :class="`bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ${getStatusColor(ticket.status)}`">
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


        </div>
    </div>
</template>




<style>
/* first column for table is smallest */
</style>
