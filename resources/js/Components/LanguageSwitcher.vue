<template>
    <select v-model="selectedLang" @change="changeLang" class="border-0" >
        <option value="en">EN</option>
        <option value="uk">UA</option>
        <option value="uk">DE</option>
    </select>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { Inertia } from '@inertiajs/inertia'

const { locale } = useI18n()
const selectedLang = ref(locale.value)

function changeLang() {
    Inertia.get(window.location.pathname, {}, {
        headers: {
            'locale': selectedLang.value,
        },
        preserveState: true,
        preserveScroll: true,
    })

    locale.value = selectedLang.value
}
</script>