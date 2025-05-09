<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MasterCard from '@/Components/MasterCard.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextH2 from '@/Components/TextH2.vue';

interface Master {
    id: number;
    main_photo: string;
    name: string;
    address: string;
    rating: number;
    description: string;
    phone: string;
    age: number;
    slug: string;
    available: boolean;
}

interface MastersResponse {
    data: Master[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

const masters = ref<MastersResponse>({
    data: [],
    current_page: 1,
    last_page: 1,
    prev_page_url: null,
    next_page_url: null,
});

const filters = ref({
    specialization: '',
    minRating: '',
    minAge: '',
    maxAge: '',
});
const applyFilters = async () => {
    let query = new URLSearchParams();

    if (filters.value.specialization) query.append('specialization', filters.value.specialization);
    if (filters.value.minRating) query.append('min_rating', filters.value.minRating);
    if (filters.value.minAge) query.append('min_age', filters.value.minAge);
    if (filters.value.maxAge) query.append('max_age', filters.value.maxAge);

    const url = `/masters?${query.toString()}`;
    await fetchMasters(url);
};

const isLoading = ref(false);

const fetchMasters = async (url = "/masters") => {
    try {
        isLoading.value = true;
        const { data } = await axios.get(url);
        masters.value = data.masters;
        updateUrl(url);
    } catch (error) {
        console.error("Помилка завантаження майстрів:", error);
    } finally {
        isLoading.value = false;
    }
};

const updateUrl = (url: string) => {
    url = url.replace('/masters', '');
    if (url !== window.location.pathname) {
        window.history.pushState({}, '', url);
    }
};

const goToPreviousPage = () => {
    if (masters.value.prev_page_url) {
        fetchMasters(masters.value.prev_page_url);
    }
};

const goToNextPage = () => {
    if (masters.value.next_page_url) {
        fetchMasters(masters.value.next_page_url);
    }
};

onMounted(() => fetchMasters());

const currentUrl = ref(window.location.href);

const getMastersTitle = () => {
    return "Список майстрів - BeautyHub";
};

const getMastersDescription = () => {
    return "Ознайомтесь зі списком кваліфікованих майстрів з різних спеціалізацій. Оберіть майстра, який найкраще відповідає вашим потребам.";
};

const getMastersImageUrl = () => {
    return "/images/masters-preview.jpg"; // Заміни на актуальний шлях до зображення
};

const getMastersUrl = () => {
    return currentUrl.value;
};
</script>

<template>
    <Head>
        <title>{{ getMastersTitle() }}</title>
        <meta name="description" :content="getMastersDescription()" />
        <meta name="keywords" content="майстри, послуги, рейтинг, адреса, краса, косметолог, масажист" />
        <link rel="canonical" :href="getMastersUrl()" />
        <meta property="og:title" :content="getMastersTitle()" />
        <meta property="og:description" :content="getMastersDescription()" />
        <meta property="og:image" :content="getMastersImageUrl()" />
        <meta property="og:url" :href="getMastersUrl()" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="BeautyHub" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="getMastersTitle()" />
        <meta name="twitter:description" :content="getMastersDescription()" />
        <meta name="twitter:image" :content="getMastersImageUrl()" />
    </Head>

    <div class="py-0 mx-auto max-w-7xl sm:px-0 lg:px-8">
        <!-- Filter Panel -->
        <div class="m-8 p-6 rounded-2xl shadow mb-8 max-w-7xl sm:px-6 lg:px-8 bg-white dark:bg-gray-800">
            <TextH2>Filters</TextH2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <TextInput model-value="filters.specialization" label="Specialization" placeholder="Example: data.services.pedicure" />
                <TextInput model-value="filters.minRating" label="Min rating" placeholder="Example: 4.5" />
                <TextInput model-value="filters.minAge" label="Min age" placeholder="Example: 25" />
            </div>
            <div class="mt-4">
                <SecondaryButton @click="applyFilters">Apply filters</SecondaryButton>
            </div>
        </div>

        <!-- Loader -->
        <div v-if="isLoading" class="text-center py-6">
            <svg class="animate-spin h-8 w-8 text-gray-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            <p class="text-gray-600 mt-2">Masters loading...</p>
        </div>

        <!-- Master Cards -->
        <div v-else>
            <div class="py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <MasterCard
                    v-for="master in masters.data"
                    :key="master.id"
                    :masterId="master.id"
                    :imageUrl="master.main_photo"
                    :name="master.name"
                    :address="master.address"
                    :rating="master.rating"
                    :description="master.description"
                    :phone="master.phone"
                    :slug="master.slug"
                    :age="master.age"
                    :available="master.available"
                />
            </div>

            <!-- Pagination Buttons -->
            <div class="flex justify-center mt-10 space-x-4">
                <SecondaryButton
                    v-if="masters.prev_page_url"
                    @click="goToPreviousPage"
                >
                    ← Prev
                </SecondaryButton>
                <span class="text-gray-500">
                    Page {{ masters.current_page }} of {{ masters.last_page }}
                </span>
                <SecondaryButton
                    v-if="masters.next_page_url"
                    @click="goToNextPage"
                >
                    Next →
                </SecondaryButton>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
