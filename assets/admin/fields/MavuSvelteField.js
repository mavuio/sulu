import React from 'react';
import { Input } from 'sulu-admin-bundle/components';

import { toJS } from 'mobx';

// from: https://github.com/Rich-Harris/react-svelte/blob/master/index.js
class SvelteComponent extends React.Component {
    constructor() {
        super();

        this.container = React.createRef();
        this.instance = null;
        this.div = React.createElement('div', {
            ref: this.container
        });
    }

    componentDidMount() {
        const {
            this: Constructor,
            ...data
        } = this.props;

        this.instance = new Constructor({
            target: this.container.current,
            props: data
        });

          
    }

    componentDidUpdate() {
        this.instance.$set(this.props);
    }

    componentWillUnmount() {
        this.instance.$destroy();
    }

    render() {
        return this.div;
    }
}





class MavuSvelteField extends React.Component {
    handleInputChange = (newValue) => {
        this.props.onChange(newValue);
        this.props.onFinish();
    };


    getCacheTimestamp() {
        return Math.floor(Date.now() / 1000 / 3600);
    }

    constructor(props) {
        super(props);
        this.state = {
            Widget: false
        };

        const svelteModulePath=props.schemaOptions.svelteModulePath.value;
        const ts = this.getCacheTimestamp();
        const cssUrl=`${svelteModulePath}.css?${ts}`;
        const jsUrl=`${svelteModulePath}.js?${ts}`;

        const cssPromise = this.loadCSS(cssUrl);
        const jsPromise = this.loadJS(jsUrl);


        Promise.all([cssPromise, jsPromise]).then(([_,module]) => {
             this.setState({Widget: module.default});
        });

    }

    loadJS(jsURL) {
         return new Promise(function (resolve, reject) {
               import(/* webpackIgnore: true */ jsURL)
                .then((module) => {
                    resolve(module);
                })
                .catch((e) => {
                    console.log('#log 3656  import rejected',jsURL,e);
                    reject()
                })
        });
    }

    loadCSS(cssURL) {
        return new Promise(function (resolve, reject) {

            var link = document.createElement('link');

            link.rel = 'stylesheet';
            link.href = cssURL;
            document.head.appendChild(link);

            link.onload = function () {
                resolve();
            };
        });
    }





    render() {
        const {
            dataPath,
            disabled,
            name,
            error,
            value
        } = this.props;

      

        if(!this.state.Widget)
        {
            return (<div>loading...</div>);
        } else {


            return ( 
            <>
                    <input
                        id={dataPath}
                        name={name}
                        onChange={this.handleInputChange}
                        type="hidden"
                        value={value == null ? '' : value}
                    />
                <SvelteComponent this={ this.state.Widget }  props={this.props} helpers={ {toJS} }/> 
            </>
            );
        }
    }
}

export default MavuSvelteField;



