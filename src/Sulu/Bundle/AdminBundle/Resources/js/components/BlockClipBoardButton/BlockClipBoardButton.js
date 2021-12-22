// @flow
import React from 'react';
import Icon from '../Icon';
import Popover from '../Popover';
import blockclipboardbuttonStyles from './blockclipboardbutton.scss';
import BlockClipBoardMenu from '../BlockClipBoardMenu';
import {action, computed, observable} from 'mobx';
import {observer} from 'mobx-react';

type Props = {|
    value: Object,
    onPasteBlocks: (items: array<object>) => void
|};


@observer
export default class BlockClipBoardButton<T: string> extends React.Component<Props<T>> {


    setDropdownButtonRef = (ref: ?ElementRef<'button'>) => {
        this.dropdownButtonRef = ref;
    };


    @action handleDropdownToggle = (event) => {
        event.stopPropagation();
        console.log('#log 1021 open', event);
        this.dropdownOpen = !this.dropdownOpen;
    };

    @action handleDropdownClose = (event) => {
        this.dropdownOpen = false;
        console.log('#log 1021 close',event, this.dropdownOpen );
    };

    ignorePopoverBackdropClick = (event) => {
        event.stopPropagation();
    };


    dropdownButtonRef: ?ElementRef<'button'>;
    @observable dropdownOpen = false;


    render() {
        const {value,onPasteBlocks} = this.props;

        return (
            <>
                <button
                className={blockclipboardbuttonStyles.blockclipboardbutton}
                onClick={this.handleDropdownToggle}
                ref={this.setDropdownButtonRef}
                        >
                <Icon name="su-more-horizontal" />
                        </button>
                     <div onClick={this.ignorePopoverBackdropClick}>
                         <Popover
                anchorElement={this.dropdownButtonRef || undefined}
                onClose={this.handleDropdownClose}
                open={this.dropdownOpen}
                        >
                {
                    (setPopoverRef, styles) => (
                        <div ref={setPopoverRef} style={styles}>
                                 <BlockClipBoardMenu  value={value} onPasteBlocks={onPasteBlocks}
/>
                        </div>
                    )
                }
                         </Popover>
                    </div>
            </>
        );
    }
}


