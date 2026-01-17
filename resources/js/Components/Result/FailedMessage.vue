<template>
    <div>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 text-left">
            <p class="text-red-700 font-bold">Thank you for your interest.</p>
            <p class="text-red-600 text-sm">
                Unfortunately, your score did not meet the required threshold.
                Please try again later.
            </p>
        </div>

        <Link href="/" class="mt-6 inline-block text-blue-600 hover:underline">
            Return to Home
        </Link>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
</script>
