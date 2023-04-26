console.log( 'armadillo is running' );
document.querySelectorAll('armadillo').forEach(( node ) => {
    const container = node.parentNode;
    const module = node.dataset.module;
    const props = JSON.parse( node.dataset.props );
    mw.loader.using( module ).then( () => {
        require( module ).armadillo(
            container,
            props,
            node.dataset.name
        ).then(() => {
            container.classList.add( 'armadillo-widget-loaded' );
        })
    })
});
