import * as urlParse from 'url-parse';

export class YoutubeVideo {

    constructor(private id,
        private title,
        private videoId,
        private lengthSeconds,
        private thumbnail
    ) { }

    public static getVideoIdByLink(link: string): string {
        const parsedLink = urlParse(link, {}, true);
        return parsedLink.query ? parsedLink.query.v : false;
    }
}
