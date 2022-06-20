<template>
   <div class="tabbar-wrapper">
      <div class='tabbar'>
         <div class="placeholder"></div>
         <button v-on:click="setTab(tab)" v-for="(tab, index) in tabs" :key="index" :class="[tab.key==selectedKey ? 'active' : 'inactive',`tab-${tab.key}`]">{{tab.label}}</button>
         <div class="placeholder"></div>
      </div>
      <div class="tabbar-stripe" :class="`tabbar-stripe-${selectedKey}`"></div>
   </div>
</template>
<script>
   export default {
      props: {
      parent_id: String
   },
   mounted: function () {
      console.log('#log 1606 mounted tabbar');
      //initially always oppen first tab
      setTimeout(() => { this.setValue(this.tabs[0].key); }, 200); 
   },
   data() {
    return {
      selectedKey:'content',
      tabs: [
         {
         key: 'content',
         label: 'Inhalte'
      },
         {
         key: 'styles',
         label: 'Styles'
      },
      ]
    }
   }, 
    computed: {
     parentEl: function () {

       return document.getElementById(this.parent_id);
     },
  },
  methods: {
     setTab:function (tab) {
       this.setValue(tab.key);
     },
     setValue: function(val) {
        const input=this.parentEl;
        this.selectedKey=val;
        var nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, "value").set;
         console.log('#log 2524 set key',this.selectedKey);

        nativeInputValueSetter.call(input, val);
        input.dispatchEvent(new Event('input', { bubbles: true }));

     },
     sendData: function(payload) {
        this.$emit('sendData', payload);
     }
  }
}
</script>
<style scoped >
.tabbar {
   display:flex;
}
.placeholder {
   flex: 1 1 auto;
   border-bottom: 1px solid #ccc;

} 
.placeholder:first-child {
   flex: 0 0 1rem;
   width: 1rem;
}

   button {
      border: 1px solid #ccc;
      border-top-left-radius: 2px;
      border-top-right-radius: 2px;
      border-right-color:transparent;
      padding:0.4rem 1rem; 
      background: none;
      cursor:pointer;
   }
   button:last-of-type {
      border-right-color: #ccc;
   }
    
   button.active {
      border-bottom-color: transparent; 
   }
   .tabbar-stripe {
      height: 15px;
      position: relative;
   }

   .tabbar-stripe:after {
   content  : "";
   position : absolute;
   z-index  : 1;
   bottom   : 0;
   left     : 0;
   pointer-events   : none;
   background-image : linear-gradient(to bottom, 
                     rgba(255,255,255, 0), 
                     rgba(255,255,255, 1) 90%);
   width    : 100%;
   height   : 15px;
   }


   .tabbar-stripe-styles,
   .tab-styles.active {
      background-color: yellow;
   }


</style>
