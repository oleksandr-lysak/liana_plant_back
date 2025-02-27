<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MasterCard from '@/Components/MasterCard.vue';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="container flex-row columns-6 text-right">
                <div class="mr-4">
                    <SecondaryButton>dsadasd</SecondaryButton>
                </div>
                <div class="mr-4">
                    <SecondaryButton>dsadasd</SecondaryButton>
                </div>
            </div>
        </template>

        <div class="py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <MasterCard
                v-for="(item, index) in 40"
                :key="index"
                imageUrl="https://images.unsplash.com/photo-1612838320302-4b3b3b3b3b3b"
                :description=item.toString()
                address="Testimonial"
                name="Name"
                phone="Position"
                :age=5
                :rating="5"
            />

        </div>
    </AuthenticatedLayout>
</template>
