function copyToClipboard(textToCopy: string) {
    ClipboardService.writeText(textToCopy).then(() => {
        console.log('hura')
    }).catch(() => console.log('kurwa!'));
}

export class ClipboardService {
    public static writeText(textToCopy: string): Promise<void> {
        return new Promise(
            (resolve, reject): void => {
                let success = false;
                const listener = (e: ClipboardEvent): void => {
                    e.clipboardData.setData('text/plain', textToCopy);
                    e.preventDefault();
                    success = true;
                };
                // some browser requires selected item
                const input = document.createElement('input');
                document.body.appendChild(input);
                input.select();

                document.addEventListener('copy', listener);
                document.execCommand('copy');
                document.removeEventListener('copy', listener);
                document.body.removeChild(input);
                success ? resolve() : reject();
            },
        );
    }
}