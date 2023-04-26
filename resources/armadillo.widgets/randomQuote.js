function randomQuote( titles ) {
    const mwApi = new mw.Api( {
        ajax: {
            url: 'https://en.wikiquote.org/w/api.php'
        }
    } );
    return mwApi.ajax( {
        format: 'json',
        origin: '*',
        action: 'query',
        redirect: true,
        titles,
        formatversion: 2
    } ).then( ( pages ) => {
        const authors = pages.query.pages;
        const author = authors[ Math.floor( Math.random() * authors.length ) ];
        const pageid = author.pageid;

        return mwApi.ajax( {
            format: "json",
            origin: '*',
            action: "parse",
            pageid
        } ).then((x) => {
            const fragment = new DocumentFragment();
            const div = document.createElement( 'div' );
            div.innerHTML = x.parse.text['*'];
            fragment.querySelectorAll( '.noprint' ).forEach((n) => n.parentNode.removeChild(n));
            fragment.appendChild( div );
            const quotes = Array.from( fragment.querySelectorAll( 'figcaption, blockquote' ) )
                // filter out quotes attributed to others.
                .filter( node => node.textContent.indexOf( '~ ' ) === -1 
            );
            const text = quotes[ Math.floor( Math.random() * quotes.length ) ].textContent;
            console.log( text, author );
            return {
                author: author.title,
                text,
                authorUrl: mw.util.getUrl( author.title )
            }
        } );
    } );
}

module.exports = randomQuote;
