// @flow
import React from 'react';
import blockclipboardmenuStyles from './blockclipboardmenu.scss';
// import BlockClipboardStore from '../../stores/BlockClipboardStore';
import userStore from '../../stores/userStore';
import { toJS} from 'mobx';

const USER_SETTING_CLIPBOARD = 'sulu_page.block_clipboard';

type Props = {|
    value: Object,
|};

export default class BlockClipBoardMenu extends React.PureComponent<Props> {

    onCopyClick(value) {
        let item=toJS(value);
        this.setClipboardItems([item]);
        console.log('#log 8534 copy',item);
    }

    onPasteClick(position) {
        const {onPasteBlocks} = this.props;
        let items=this.getClipboardItems().map( item => toJS(item));
        console.log('#log 7987 paste',items);
        onPasteBlocks(items);
    }

    setClipboardItems(items) {
        userStore.setPersistentSetting(USER_SETTING_CLIPBOARD, items);
    }

    getClipboardItems() {

        return userStore.getPersistentSetting(USER_SETTING_CLIPBOARD);
    }

    copy_icon=(
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
    )
    cut_icon=(
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path></svg>
    )
    duplicate_icon=(
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
    )

    multiselect_icon=(<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>)

    render() {
        const {value} = this.props;

        return (
            <ul className={blockclipboardmenuStyles.blockclipboardmenu}>
                    <li ><span>{this.multiselect_icon}</span>select multiple</li>
                    <li  onClick={() => this.onCopyClick(value)}><span>{this.copy_icon}</span>copy to clipboard</li>
                    <li  onClick={() => this.onCopyClick(value)}><span>{this.cut_icon}</span>move to clipboard ("cut")</li>
                    <li  onClick={() => this.onCopyClick(value)}><span>{this.duplicate_icon}</span>duplicate</li>

                    {/* <li onClick={ () => this.onPasteClick('before')} >paste n elementfrom clipboard BEFORE this element</li>
                    <li onClick={ () => this.onPasteClick('after')}>paste n element(s) from clipboard AFTER this element</li> */}
                    {/* <li >create new element BEFORE this element</li> */}
                    {/* <li >create new element AFTER this element</li> */}
            </ul>);
    }
}
