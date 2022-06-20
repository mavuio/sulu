import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

import { SVGInjector } from '@tanem/svg-injector'

import expander from './van11y-accessible-hide-show-aria.js'

import SwiperComponent from './alpineComponents/swiper/swiperComponent'

import StripesComponent from './alpineComponents/stripes/stripesComponent'

import GlightboxComponent from './alpineComponents/glightbox/glightboxComponent'


window.Alpine = Alpine

const alpineComponents = {
    GlightboxComponent,
    SwiperComponent,
    StripesComponent
};

import "regenerator-runtime/runtime.js";



Alpine.data('loadComponent', (componentname, args)=> alpineComponents[`${componentname}Component`](args) );
Alpine.plugin(collapse)
Alpine.start();


document.addEventListener("DOMContentLoaded", function() {
// Elements to inject
var mySVGsToInject = document.querySelectorAll('img.svg-icon');

console.log('#log 8530', mySVGsToInject);
// Do the injection
SVGInjector(mySVGsToInject);
});





window.expander = expander();

var loadExpander = function loadExpander() {
    var expand_default = window.expander();
    expand_default.attach();

    document.removeEventListener('DOMContentLoaded', loadExpander);
};

document.addEventListener('DOMContentLoaded', loadExpander);

