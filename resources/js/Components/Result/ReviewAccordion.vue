<template>
    <div
        class="mb-6 sm:mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
    >
        <AccordionHeader 
            :is-open="reviewOpen" 
            @toggle="reviewOpen = !reviewOpen" 
        />

        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform -translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="reviewOpen" class="bg-gray-50 p-4 sm:p-6">
                <div class="space-y-4 sm:space-y-6">
                    <QuestionReviewCard
                        v-for="question in questions"
                        :key="question.question_number"
                        :question="question"
                    />
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref } from "vue";
import AccordionHeader from "./AccordionHeader.vue";
import QuestionReviewCard from "./QuestionReviewCard.vue";

defineProps({
    questions: {
        type: Array,
        required: true,
    },
});

const reviewOpen = ref(false);
</script>
