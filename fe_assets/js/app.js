import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

import { SVGInjector } from '@tanem/svg-injector'

import SwiperComponent from './alpineComponents/swiper/swiperComponent'

import SvelteComponent from './alpineComponents/svelte/svelteComponent'

import StripesComponent from './alpineComponents/stripes/stripesComponent'

import GlightboxComponent from './alpineComponents/glightbox/glightboxComponent'


window.Alpine = Alpine

const alpineComponents = {
    GlightboxComponent,
    SwiperComponent,
    StripesComponent,
    SvelteComponent
};

import "regenerator-runtime/runtime.js";


Alpine.data('loadComponent', (componentname, args)=> alpineComponents[`${componentname}Component`](args) );
Alpine.plugin(collapse)
Alpine.start();

document.addEventListener("DOMContentLoaded", function() {
// Elements to inject
var mySVGsToInject = document.querySelectorAll('img.svg-icon,img[src*=".svg"]:not(.svg-as-image)');

// console.log('#log 8530', mySVGsToInject);
// Do the injection
SVGInjector(mySVGsToInject);
});


window.share=function(url) {
    navigator
    .share({
        url: url
    })
//     .then(() => console.log('Successful share! 🎉'))
//     .catch(err => console.error(err));
}

