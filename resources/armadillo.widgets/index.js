async function fetchTFA() {
	const currentTime = new Date();
	const currentDD = String(currentTime.getDate()).padStart(2, '0');
	const currentMM = String(currentTime.getMonth() + 1).padStart(2, '0');
	const currentYYYY = currentTime.getFullYear();
	const baseApiUrl = 'https://en.wikipedia.org/api/rest_v1/feed/featured';
	try {
		const response = await fetch( `${baseApiUrl}/${currentYYYY}/${currentMM}/${currentDD}` );
		const jsonData = await response.json();
		return jsonData.tfa;
	} catch ( error ) {
		console.error( `Download error: ${error.message}` );
		return null;
	}
};

async function buildTFA() {
	const tfa = await fetchTFA();
	const tfaTemplateString = `
		<div class="MainPageBG mp-box">
			<h2 class="mp-h2"><span id="From_today.27s_featured_article"></span><span class="mw-headline" id="From_today's_featured_article">From today's featured article</span></h2>
			<div style="float: left; margin: 0.5em 0.9em 0.4em 0em;">
				<div class="thumbinner mp-thumb" style="background: transparent; border: none; padding: 0; max-width: 140px;">
					<a class="image" title=${tfa.titles.normalized}">
						<img alt=${tfa.titles.normalized}" src="${tfa.originalimage.source}" data-file-width="1080" data-file-height="1081" width="140" height="140">
					</a>
					<div class="thumbcaption" style="padding: 0.25em 0; word-wrap: break-word;">${tfa.titles.normalized}</div>
				</div>
			</div>
			${tfa.extract_html}
		</div>
	`;
	return document.createRange().createContextualFragment( tfaTemplateString );
};

async function tfa( el ) {
	el.appendChild( await buildTFA() );
}

function quote( el, props ) {
    el.innerHTML = props.html;
}

function armadillo( el ) {
	el.innerHTML = `<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Nine-banded_Armadillo.jpg/800px-Nine-banded_Armadillo.jpg?20170615182855">`;
}

module.exports = {
	armadillo: function ( el, props, name ) {
		switch ( name ) {
			case 'quote':
				quote( el, props );
				break;
			case 'tfa':
				tfa( el );
				break;
			default:
				armadillo( el );
				break;
		}
		return Promise.resolve();
	}
}
