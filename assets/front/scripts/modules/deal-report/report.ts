window.addEventListener('load', () => {
    const expiredBtns: NodeListOf<HTMLElement> = document.querySelectorAll<HTMLElement>('[data-report-deal]');
    expiredBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const dealID: string = btn.getAttribute('data-deal-id');
            const userID: string = btn.getAttribute('data-user-id');

            if (dealID && userID) {
                let formData = new FormData();
                formData.set('userID', userID);
                // TODO Replace with FOSJsRoutingBundle
                fetch('/deals/'+dealID+'/report', {
                    method: "POST",
                    body: formData,
                });
            }
        })
    });
});