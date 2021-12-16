// @flow
import React, {Fragment} from 'react';
import type {CustomProps} from './types';


const $script = require("scriptjs")



export default class Custom<T: ?string | ?number> extends React.PureComponent<CustomProps<T>> {


    constructor(props) {
        super(props);
        this.state = {Component: null};

        $script("/tab_bar/mavu-tabbar.js", () => {
            this.setState({
                Component: ({ data, children, ...props}) => React.createElement(
                    'mavu-tabbar',
                    {...props, data: JSON.stringify(data), ref: elem => this[name] = elem },
                    children
                )
            });
            // this[name].addEventListener('sendData', event => {
            //     const payload = event.detail
            //     payload && payload.length && sendData(payload[0])
            // });
        });
      }


    render() {
        const {
            value,
        } = this.props;

        if(this.state.Component)
        {
            return (<this.state.Component  {...this.props} />);
        } else {
            return (<div>loading...</div>)
        }
    }
}
