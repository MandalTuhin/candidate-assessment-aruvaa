<template>
    <section
        class="mb-4 sm:mb-6 p-2 sm:p-3 bg-yellow-50 border border-yellow-200 rounded-lg"
        aria-label="Assessment timer"
        role="timer"
    >
        <div class="flex justify-between items-center">
            <span class="text-xs sm:text-sm font-medium text-gray-700"
                >Time Remaining:</span
            >
            <span
                class="text-base sm:text-lg font-bold"
                :class="{
                    'text-red-600': timeRemaining <= 60,
                    'text-yellow-600':
                        timeRemaining <= 120 && timeRemaining > 60,
                    'text-green-600': timeRemaining > 120,
                }"
                aria-live="polite"
                :aria-label="`Time remaining: ${formatTime(timeRemaining)}`"
            >
                {{ formatTime(timeRemaining) }}
            </span>
        </div>
    </section>
</template>

<script setup>
defineProps({
    timeRemaining: Number,
});

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
};
</script>
