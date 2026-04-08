<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const form = ref({
    title: "",
    description: "",
    starting_price: "",
    start_time: "",
    end_time: "",
    image: null,
});

const submit = () => {
    router.post("/auctions", form.value, {
        forceFormData: true,
    });
};
</script>

<template>
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-xl font-bold mb-4">Create Auction</h1>

        <input v-model="form.title" placeholder="Title" class="input" />

        <textarea
            v-model="form.description"
            placeholder="Description"
            class="input"
        />

        <input
            v-model="form.starting_price"
            type="number"
            placeholder="Starting Price"
            class="input"
        />

        <input v-model="form.start_time" type="datetime-local" class="input" />
        <input v-model="form.end_time" type="datetime-local" class="input" />

        <input type="file" @change="(e) => (form.image = e.target.files[0])" />

        <button @click="submit" class="btn">Create</button>
    </div>
</template>
