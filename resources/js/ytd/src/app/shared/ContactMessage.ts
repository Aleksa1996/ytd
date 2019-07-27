
export class ContactMessage {
    constructor(
        public name,
        public email,
        public message,
        public subject = 'Youtube downloader contact form',
    ) { }
}
