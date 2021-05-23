import Vue from 'vue';

window.addEventListener('load', () => {
    if (document.getElementById('previewed')) {
        setupVuePreviewer();
    }
})


const setupVuePreviewer = () => {
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
                promoType: '',
            }
        }
    });
}



