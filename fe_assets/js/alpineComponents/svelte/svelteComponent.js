export default ([path, props, options]) => ({  
    myapp: 'unset',      
    getCacheTimestamp() {
 
        return new Date().toISOString().slice(0,15).replaceAll(/[^0-9]/g,'')+'0';
 
    },
    init() {

        const ts = this.getCacheTimestamp();

        if(options && options.livePath) {
            path=`https://hortencollection.com.test:3000${options.livePath}`;
        } else{
            path+=`?ts=${ts}`;
        }
        import(/* webpackIgnore: true */path)
        .then((module) => {
            if(this.$el.childNodes.length===0) {
                this.myapp= new module.default({ target: this.$el, props: props });
            }
        })
        .catch((e) => console.warn(e))
    },
    destroy() {

    }
  });


