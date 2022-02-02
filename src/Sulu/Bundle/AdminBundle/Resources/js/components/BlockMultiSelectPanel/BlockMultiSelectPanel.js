// @flow
import React from 'react';
import blockmultiselectpanelStyles from './blockMultiselectPanel.scss';
import BlockClipboard from '../../services/BlockClipboard';
import {observer} from 'mobx-react';
import {action} from 'mobx';
import Button from '../Button';




type Props = {|
    multiselect: Object,
    allBlocks: Array<number>,
|};

@observer
export default class BlockMultiSelectPanel extends React.Component<Props> {

    @action cancelClick() {
        const {multiselect} = this.props;
        multiselect.enabled=false;
    }

    @action selectAllClick(indexes) {
        const {multiselect,allBlocks} = this.props;
        multiselect.selectedIndexes=[...Array.from(allBlocks.keys())];
    }


    @action selectNoneClick() {
        const {multiselect} = this.props;
        multiselect.selectedIndexes=[];
    }


    @action copyClick() {
        const {multiselect, allBlocks} = this.props;
        BlockClipboard.setClipboardItems(multiselect.selectedIndexes.map( idx => allBlocks[idx] ));
        this.cancelClick()
    }


    @action deleteClick() {
        const {multiselect, onBlockMenuClick} = this.props;
        onBlockMenuClick("delete_multiple",{positions: multiselect.selectedIndexes});
        multiselect.selectedIndexes=[];
        this.cancelClick()
    }

    @action cutClick() {
        const {multiselect, allBlocks} = this.props;
        this.copyClick();
        this.deleteClick();
    }
    @action hideClick() {
        const {multiselect, onBlockMenuClick} = this.props;
        multiselect.selectedIndexes.forEach(position => {
            onBlockMenuClick("hide",{position});
        });
    }
    @action unhideClick() {
        const {multiselect, onBlockMenuClick} = this.props;
        multiselect.selectedIndexes.forEach(position => {
            onBlockMenuClick("unhide",{position});
        });
    }



    render() {
        const {multiselect} = this.props;
        return (
            <div className={blockmultiselectpanelStyles.blockmultiselectpanel}>

                <section>
                    <Button icon="su-times" onClick={() => this.cancelClick()} size="large" skin="link"></Button>
                    <Button onClick={() => this.selectAllClick()} size="small" skin="icon"> select all </Button>
                    <Button onClick={() => this.selectNoneClick()} size="small" skin="icon"> select none</Button>
                    <Button onClick={() => this.copyClick()} size="small" skin="icon" disabled={multiselect.selectedIndexes.length==0}> copy </Button>
                    <Button onClick={() => this.cutClick()} size="small" skin="icon" disabled={multiselect.selectedIndexes.length==0}> cut </Button>
                    <Button onClick={() => this.deleteClick()} size="small" skin="icon" disabled={multiselect.selectedIndexes.length==0}> delete </Button>
                    <Button onClick={() => this.hideClick()} size="small" skin="icon" disabled={multiselect.selectedIndexes.length==0}> hide </Button>
                    <Button onClick={() => this.unhideClick()} size="small" skin="icon" disabled={multiselect.selectedIndexes.length==0}> unhide </Button>
                </section>
            </div>
            );
    }
}
