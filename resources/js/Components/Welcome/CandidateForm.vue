<template>
    <section class="mb-6 space-y-4" aria-label="Candidate information">
        <div>
            <label for="candidate-name" class="block text-sm font-medium text-gray-700">
                Full Name
            </label>
            <input
                id="candidate-name"
                type="text"
                :value="name"
                @input="$emit('update:name', $event.target.value)"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500"
                aria-describedby="name-help"
                autocomplete="name"
            />
            <span id="name-help" class="sr-only">Enter your full name as it appears on official documents</span>
        </div>
        <div>
            <label for="candidate-email" class="block text-sm font-medium text-gray-700">
                Email Address
            </label>
            <input
                id="candidate-email"
                type="email"
                :value="email"
                @input="$emit('update:email', $event.target.value)"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-blue-500 focus:border-blue-500"
                aria-describedby="email-help"
                autocomplete="email"
            />
            <span id="email-help" class="sr-only">Enter a valid email address for receiving assessment results</span>
        </div>
    </section>
</template>

<script setup>
defineProps({
    name: String,
    email: String,
});

defineEmits(["update:name", "update:email"]);
</script>
