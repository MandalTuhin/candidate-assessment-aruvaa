<template>
  <div class="mb-6 space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">
        Full Name
      </label>
      <input
        type="text"
        :value="name"
        @input="$emit('update:name', $event.target.value)"
        required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"
      />
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">
        Email Address
      </label>
      <input
        type="email"
        :value="email"
        @input="$emit('update:email', $event.target.value)"
        required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border"
      />
    </div>
  </div>
</template>

<script setup>
defineProps({
  name: String,
  email: String
})

defineEmits(['update:name', 'update:email'])
</script>
