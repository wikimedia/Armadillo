function armadillo( el ) {
    el.innerHTML = `<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Nine-banded_Armadillo.jpg/800px-Nine-banded_Armadillo.jpg?20170615182855">`;
}

function quote( el, props ) {
    el.textContent = props.text;
}


module.exports = {
    armadillo: function ( el, props, name ) {
        switch ( name ) {
            case 'quote':
                quote( el, props );
                break;
            default:
                armadillo( el );
                break;
        }
        return Promise.resolve();
    }
}
