document.addEventListener('alpine:init', () => {
    Alpine.data('currentResponseData', () => ({
        init() {
            const observer = new MutationObserver((mutations) =>{
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        const converter = new showdown.Converter();
                        document.getElementById('current_response').innerHTML = converter.makeHtml(
                            // sanitizeHtml(document.getElementById('hidden_current_response').innerText)
                            document.getElementById('hidden_current_response').innerText
                        );
                    }
                });
            });

            observer.observe(document.getElementById('hidden_current_response'), { attributes: false, childList: true, characterData: false, subtree: true });
        },
    }))
});