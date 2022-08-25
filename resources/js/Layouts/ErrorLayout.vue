<template>
    <Nav/>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <slot/>
            </div>
        </div>
    </div>
</template>

<script>
import Nav from '@/Shared/Nav';

export default {
    name: "ErrorLayout",
    components: { Nav },
};
</script>
