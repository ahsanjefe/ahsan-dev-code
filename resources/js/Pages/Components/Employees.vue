<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { GithubIcon } from '@/Components/Icons/brands'
</script>

<template>
    <AuthenticatedLayout title="Employees">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Employees
                </h2>
            </div>
        </template>
        
    </AuthenticatedLayout>
</template>
