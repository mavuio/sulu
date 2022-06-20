async function loadModule(message) {
    const {
        default: myModule
    } = await import(
        /* webpackChunkName: "glightbox" */
        "./loadGlightbox"
    );
    return myModule;
}



export default (options)=>({
    options,
    glightboxInstance: null,
    async init() {
        if (!window.Glightbox) {
            window.Glightbox = await loadModule();
            this.setupGlightbox();
        }
    },
    setupGlightbox() {
        // console.log('#log 3831 setup glightbox',this.$el,{...this.options}, window.Glightbox);
        this.glightboxInstance = window.Glightbox({...this.options});
        // console.log('#log 1657 glightboxInstance',this.glightboxInstance);
    },
    openLightbox(idx=0) {
        // console.log(`#log 4157 openLightbox ${idx}`);
        this.glightboxInstance.openAt(idx);
    }
    // destroy() {
    //     console.log('#log 9515 destroy glightbox hook');
    //     delete window.Glightbox;
    //     delete window.glightbox_waitlist;
    // },

});


