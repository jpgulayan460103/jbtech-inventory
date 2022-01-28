<template>
    <div class="container">
        
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getItem();
        },
        props: ['warehouses'],
        data() {
            return {
            
            }
        },
        methods: {
            async getItem(){
                let res = await axios.get('/api/items',{
                    params: this.itemFilterData
                });
                this.items = res.data.data;
                this.itemPaginations = res.data.links;
            },
        }
    }
</script>