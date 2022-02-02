// @flow
import React from 'react';
import blockclipboardmenuStyles from './blockclipboardmenu.scss';
import BlockClipboard from '../../services/BlockClipboard';

// import BlockClipboardStore from '../../stores/BlockClipboardStore';
import userStore from '../../stores/userStore';
import { toJS} from 'mobx';



type Props = {|
    value: Object,
    sortIndex: number,
|};

export default class BlockClipBoardMenu extends React.PureComponent<Props> {

    close() {
        const {handleDropdownClose} = this.props;
        handleDropdownClose(null);
    }

    onStartMultiselectClick() {
        const {onBlockMenuClick} = this.props;
        onBlockMenuClick("selection_command",{command: "start_multiselect"});
        this.close();
    }

    onCopyClick(value) {
        let item=toJS(value);
        BlockClipboard.setClipboardItems([item]);
        this.close();
    }

    onCutClick(value,position) {
        this.onCopyClick(value)
        this.onDeleteClick(position)
    }

    onPasteClick(position) {
        const {onBlockMenuClick} = this.props;
        let items=BlockClipboard.getClipboardItems().map( item => toJS(item));
        onBlockMenuClick("insert",{items,position});
        this.close();
    }

    onDuplicateClick(value,position) {
        const {onBlockMenuClick} = this.props;
        onBlockMenuClick("insert",{items: [toJS(value)], position: position+1});
        this.close();
    }

    onDeleteClick(position) {
        const {onBlockMenuClick} = this.props;
        onBlockMenuClick("delete",{position});
    }

    onHideClick(position) {
        const {onBlockMenuClick} = this.props;
        onBlockMenuClick("hide",{position});
        this.close();
    }

    onUnhideClick(position) {
        const {onBlockMenuClick} = this.props;
        onBlockMenuClick("unhide",{position});
        this.close();
    }




    copy_icon=(
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
    )
    cut_icon=(
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path></svg>
    )
    duplicate_icon=(
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
    )
    paste_icon=(
        <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
    )

    trash_icon=(
        <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
    )


    unhide_icon=(
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
    )


    hide_icon=(
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
    )



    multiselect_icon=(<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>)

    render() {
        const {value,sortIndex} = this.props;
        console.log('#log 4849 MENU ON ', value);
        return (
            <ul className={blockclipboardmenuStyles.blockclipboardmenu}>
                    <li onClick={() => this.onStartMultiselectClick()} ><span>{this.multiselect_icon}</span>select multiple</li>
                    <li onClick={() => this.onCopyClick(value)}><span>{this.copy_icon}</span>copy</li>
                    <li onClick={() => this.onCutClick(value,sortIndex)}><span>{this.cut_icon}</span>cut</li>
                    <li onClick={() => this.onDuplicateClick(value,sortIndex)}><span>{this.duplicate_icon}</span>duplicate</li>
                    <li onClick={() => this.onPasteClick(sortIndex)} ><span>{this.paste_icon}</span>↑ paste before</li>
                    <li onClick={() => this.onPasteClick(sortIndex+1)}><span>{this.paste_icon}</span>↓ paste after</li>
                    <li onClick={() => this.onDeleteClick(sortIndex)}><span>{this.trash_icon}</span>delete</li>
                    {value?.settings?.hidden ?<li onClick={() => this.onUnhideClick(sortIndex)}><span>{this.unhide_icon}</span>unhide</li>:<li onClick={() => this.onHideClick(sortIndex)}><span>{this.hide_icon}</span>hide</li>}

            </ul>);
    }
}
