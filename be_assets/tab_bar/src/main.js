import Vue from 'vue'
import wrap from '@vue/web-component-wrapper'
import Tabbar from './components/Tabbar';
const CustomElement = wrap(Vue, Tabbar)
window.customElements.define('mavu-tabbar', CustomElement)
