import * as urlParse from 'url-parse';

export class YoutubeVideo {

    constructor(public id,
        public title,
        public videoId,
        public lengthSeconds,
        public thumbnail,
        public requested,
        public lastRequest,
        public status,
    ) { }

    public static getVideoIdByLink(link: string): string {
        const parsedLink = urlParse(link, {}, true);
        return parsedLink.query ? parsedLink.query.v : false;
    }
}
