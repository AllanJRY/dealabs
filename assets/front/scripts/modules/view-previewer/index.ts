import Vue from 'vue';

const vuePreviewer = new Vue({
    el: '#previewed',
    data() {
        return {
            title: '',
            description: '',
            link: '',
            promoCode: '',
            price: '',
            initialPrice: '',
            shippingCost: '',
            freeShipping: false,
            partner: '',
            categories: [],
            picture: '',
        }
    }
});
