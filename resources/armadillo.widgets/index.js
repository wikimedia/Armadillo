let tfaDateOffset = 0;
function changeOffset( val ) {
	tfaDateOffset += val;
}

function getDate() {
	var date = new Date();
	date.setDate( date.getDate() + tfaDateOffset );
	const DD = String(date.getDate()).padStart(2, '0');
	const MM = String(date.getMonth() + 1).padStart(2, '0');
	const YYYY = date.getFullYear();
	return [ YYYY, MM, DD ];
}

async function fetchTFA() {
	const date = getDate();
	const baseApiUrl = 'https://en.wikipedia.org/api/rest_v1/feed/featured';
	try {
		console.log(`${baseApiUrl}/${date[0]}/${date[1]}/${date[2]}`)
		const response = await fetch( `${baseApiUrl}/${date[0]}/${date[1]}/${date[2]}` );
		const jsonData = await response.json();
		return jsonData.tfa;
	} catch ( error ) {
		console.error( `Download error: ${error.message}` );
		return null;
	}
};

async function buildTFA() {
	const tfa = await fetchTFA();
	const tfaTemplate = `
		<div id="tfa" class="MainPageBG mp-box">
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
	return document.createRange().createContextualFragment( tfaTemplate );
};

async function tfa( el ) {
	el.appendChild( await buildTFA() );
	const tfaButtonTemplate = `
		<div style="clear: both">
			<button id="previous-tfa" class="mw-ui-button"> Previous featured article </button>
			<button id="next-tfa" class="mw-ui-button"> Next featured article </button>
		</div>
	`;
	const buttonElements = document.createRange().createContextualFragment( tfaButtonTemplate );
	el.appendChild( buttonElements );
	document.getElementById('previous-tfa').addEventListener( 'click', async function ( ) {
		const oldTFA = document.getElementById( 'tfa' );
		changeOffset( -1 );
		oldTFA.parentElement.replaceChild( await buildTFA(), oldTFA );
	} );
	document.getElementById('next-tfa').addEventListener( 'click', async function ( ) {
		const oldTFA = document.getElementById( 'tfa' );
		changeOffset( 1 );
		oldTFA.parentElement.replaceChild( await buildTFA(), oldTFA );
	} );
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
