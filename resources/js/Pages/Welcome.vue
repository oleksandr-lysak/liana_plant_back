<script lang="ts" setup>

import { Head } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MasterCard from '@/Components/MasterCard.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';

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
}

interface MastersResponse {
    data: Master[];
    prev_page_url: string | null;
    next_page_url: string | null;
}

const masters = ref<MastersResponse>({
    data: [],
    prev_page_url: null,
    next_page_url: null,
});

const fetchMasters = async (url = "/masters") => {
    try {
        const { data } = await axios.get(url);
        masters.value = data.masters;
        updateUrl(url);
    } catch (error) {
        console.error("Помилка завантаження майстрів:", error);
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
    return "Список майстрів - [Назва вашого сайту]";
};

const getMastersDescription = () => {
    return "Ознайомтесь зі списком кваліфікованих майстрів з різних спеціалізацій. Оберіть майстра, який найкраще відповідає вашим потребам.";
};

const getMastersImageUrl = () => {
    // Ви можете використовувати логотип вашого сайту або загальне зображення для списку майстрів
    return "URL_вашого_зображення";
};

const getMastersUrl = () => {
    return currentUrl.value;
};

</script>


<template>
    <Head>
        <title>{{ getMastersTitle() }}</title>
        <meta name="description" :content="getMastersDescription()" />
        <meta name="keywords" content="майстри, послуги, рейтинг, адреса, [ваші ключові слова]" />
        <link rel="canonical" :href="getMastersUrl()" />
        <meta property="og:title" :content="getMastersTitle()" />
        <meta property="og:description" :content="getMastersDescription()" />
        <meta property="og:image" :content="getMastersImageUrl()" />
        <meta property="og:url" :href="getMastersUrl()" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="[Назва вашого сайту]" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="getMastersTitle()" />
        <meta name="twitter:description" :content="getMastersDescription()" />
        <meta name="twitter:image" :content="getMastersImageUrl()" />
    </Head>
    

    
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
            />

            <div class="flex justify-between mt-4">
                <button
                    v-if="masters.prev_page_url"
                    @click="goToPreviousPage"
                    class="px-4 py-2 bg-gray-300 rounded"
                >
                    Назад
                </button>

                <button
                    v-if="masters.next_page_url"
                    @click="goToNextPage"
                    class="px-4 py-2 bg-gray-300 rounded"
                >
                    Вперед
                </button>
            </div>
        </div>
    
</template>