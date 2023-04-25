console.log( 'armadillo is running' );
document.querySelectorAll('armadillo').forEach(( node ) => {
    const module = node.dataset.module;
    const props = JSON.parse( node.dataset.props );
    mw.loader.using( module ).then( () => {
        require( module ).armadillo(
            node.parentNode,
            props
        );
    })
});
