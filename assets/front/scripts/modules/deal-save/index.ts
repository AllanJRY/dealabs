window.addEventListener('load', () => {
    const saveBtns: NodeListOf<HTMLElement> = document.querySelectorAll<HTMLElement>('[data-save-deal]');
    saveBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const dealID: string = btn.getAttribute('data-deal-id');
            const userID: string = btn.getAttribute('data-user-id');

            if (dealID && userID) {
                let formData = new FormData();
                formData.set('userID', userID);
                // TODO Replace with FOSJsRoutingBundle
                fetch('/deals/'+dealID+'/save', {
                    method: "POST",
                    body: formData,
                }).then(rawResponse => {
                    return rawResponse.json();
                }).then(response => {
                    console.log(response);
                });
            }
        })
    });
});
