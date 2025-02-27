<script lang="ts" setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
        url = url.replace('/masters', '');
        if (url !== window.location.pathname) {
            window.history.pushState({}, '', url);
        }
    } catch (error) {
        console.error("Помилка завантаження майстрів:", error);
    }
};

onMounted(() => fetchMasters());

</script>

<template>
    <Head title="Welcome" />

    <AuthenticatedLayout>
        <template #header>
            <div class="container flex-row columns-6 text-right">
                <div class="mr-4">
                    <SecondaryButton>Кнопка 1</SecondaryButton>
                </div>
                <div class="mr-4">
                    <SecondaryButton>Кнопка 2</SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <MasterCard
                v-for="master in masters.data"
                :key="master.id"
                :imageUrl="master.main_photo"
                :name="master.name"
                :address="master.address"
                :rating="master.rating"
                :description="master.description"
                :phone="master.phone"
                :age="master.age"
            />

            <!-- Пагінація -->
            <div class="flex justify-between mt-4">
                <button
                    :disabled="!masters.prev_page_url"
                    @click="fetchMasters(masters.prev_page_url || '/masters')"
                    class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
                >
                    Назад
                </button>

                <button
                    :disabled="!masters.next_page_url"
                    @click="fetchMasters(masters.next_page_url || '/masters')"
                    class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
                >
                    Вперед
                </button>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
