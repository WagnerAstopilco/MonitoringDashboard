<script setup>

import publicNavBar from '@/components/layout/publicNavBar.vue'
import Footer from '@/components/layout/footer.vue'

</script>


<template>

<div class="d-flex flex-column min-vh-100">

    <publicNavBar />

    <main class="flex-grow-1 container-fluid py-4">

        <RouterView />

    </main>
    
    <Footer />
</div>

</template>