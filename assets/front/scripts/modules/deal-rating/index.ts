// FIXME
// const routes = require('/public/js/fos_js_routes.json');
// import Routing from '/vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
//
// Routing.setRoutingData(routes);

window.addEventListener('load', () => {
    const ratings: NodeListOf<Element> = document.querySelectorAll('[data-rating-btn]');
    if(ratings.length > 0) {
        ratings.forEach(ratingBtn => {
            ratingBtn.addEventListener('click', () => {
                const userID: string = ratingBtn.getAttribute('data-user-id');
                if(userID) {
                    const dealID: string = ratingBtn.getAttribute('data-deal-id');
                    const value: number = parseInt(ratingBtn.getAttribute('data-value'));
                    const dealHotValueElement: HTMLElement = ratingBtn.parentElement.querySelector<HTMLElement>('[data-deal-hot-value]');
                    // FIXME should take into account that a change between 1 to - should down 2 times

                    if (dealID && value) {
                        // FIXME
                        // Routing.generate('rate_deal', { id: dealID })
                        let formData = new FormData();
                        formData.set('userID', userID);
                        formData.set('value', value.toString());
                        fetch('/deals/rate/'+dealID, {
                            method: "POST",
                            body: formData,
                        }).then(responseString => {
                            if(responseString.status !== 200) {
                                console.warn("Unable to rate the deal");
                            }
                            return responseString.json();
                        }).then(response => {
                            dealHotValueElement.setAttribute('value', response.hotValue);
                            dealHotValueElement.innerText = response.hotValue+'°';
                        });
                    }
                }
            });
        });
    }
});
