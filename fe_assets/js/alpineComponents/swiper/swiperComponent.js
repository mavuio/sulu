async function loadModule(message) {
    const {
        default: myModule
    } = await import(
        /* webpackChunkName: "swiper" */
        "./loadSwiper"
    );
    return myModule;
}



export default (options)=>({
    options,
    swiperInstance: null,
    async init() {
        if (!window.Swiper) {
            window.Swiper = await loadModule();
            this.setupSwiper();
        }
    },
    setupSwiper() {
        // console.log('#log 3831 setup swiper',this.$el,this.options, window.Swiper);
        this.swiperInstance = new window.Swiper(this.$el, this.options);
        // console.log('#log 1657',this.swiperInstance);
    }
    // destroy() {
    //     console.log('#log 9515 destroy swiper hook');
    //     delete window.Swiper;
    //     delete window.swiper_waitlist;
    // },

});


